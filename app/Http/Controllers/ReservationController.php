<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use App\payment\PaypalGateway;
use App\Repositories\PropertyRepository;
use App\Repositories\ReservationRepository;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ReservationController extends Controller
{

    /**
     * @var ReservationRepository
     */
    private ReservationRepository $reservationRepository;
    private PropertyRepository $propertyRepository;
    private PaypalGateway $paypalGateway;

    public function __construct(
        ReservationRepository $reservationRepository,
        PropertyRepository $propertyRepository,PaypalGateway $paypalGateway)
    {
        $this->reservationRepository = $reservationRepository;
        $this->propertyRepository = $propertyRepository;
        $this->paypalGateway = $paypalGateway;
    }

    public function reservationByUserLoc(Request $request): JsonResponse
    {
        $user = auth()->user();
        $reservations = DB::table('reservations as r')
            ->select('r.id', 'r.property_id', 'p.name', 'r.price', 'p.hosting_capacity', 'r.created_at' )
            ->join('properties as p', 'r.property_id', '=', 'p.id')
            ->where('r.user_id', $user?->id)
            ->get();
        return  new JsonResponse($reservations);
    }

    public function reservationByUserProp(Request $request): JsonResponse
    {
        $user = auth()->user();
        $reservations = DB::table('reservations as r')
            ->select('r.id', 'r.property_id', 'p.name', 'r.price', 'p.hosting_capacity', 'r.created_at' )
            ->join('properties as p', 'r.property_id', '=', 'p.id')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->where('p.user_id', $user?->id)
            ->get();
        return  new JsonResponse($reservations);
    }


    /**
     * @throws \Exception
     */
    public function check(Request $request): JsonResponse|\Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'arrival' => 'required|date|after:today',
            'departure' => 'required|date|after:arrival',
            'propertyId' => 'required|numeric|exists:properties,id',

        ]);
        if($validator->fails()){
            return new JsonResponse($validator->errors());
        }

        $arrival = $request->arrival ;
        $departure = $request->departure ;
        $propertyId = $request->propertyId ;


        $reservations = $this->reservationRepository->findReservationbyAll($arrival,$departure,$propertyId);

       if(count($reservations) == 0)
        $reservations = $this->reservationRepository->findReservationByArrival($arrival,$departure,$propertyId);
        if(count($reservations) == 0)
            $reservations = $this->reservationRepository->findReservationByDeparture($arrival,$departure,$propertyId);

        if(count($reservations)>1)
            return new JsonResponse([
                "status" => "negative",
                "error" => "can't reserve at this dates"
            ],200);

        if(count($reservations) == 1){

            if(!($reservations[0]->arrival_date >= new DateTimeImmutable($departure) ) && $reservations[0]->departure_date != new DateTimeImmutable($arrival) )
                return new JsonResponse([
                    "status" => "negative",
                    "error" => "can't reserve at this dates"
                ],200);
        }

        $property = $this->propertyRepository?->getById($request->propertyId,['price','check_in_at','check_out_at'])?->with('address', 'user')->first();

        $arrivalDate = new DateTimeImmutable($request->arrival.' '.$property->check_in_at) ;
        $departureDate= new DateTimeImmutable($request->departure.' '.$property->check_out_at) ;
        $user = auth()->user() ;
        $days = $departureDate->diff($arrivalDate)->days;
        $orderUrl = URL::temporarySignedRoute('order.create',now()->addMinute(10));
        return new JsonResponse([
            "status" => "affirmative",
            "days" => $days,
            "price" => $property->price * $days,
            "orderUrl" =>  $orderUrl,
            "property" => $property,
            "PAYPAL_CLIENT_ID" => env('PAYPAL_CLIENT_ID')
        ],200);

    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(Request $request,PropertyRepository $propertyRepository ): JsonResponse
    {

        $validator = Validator::make($request->all(),[
            'arrival' => 'required|date|after:today',
            'departure' => 'required|date|after:arrival',
            'propertyId' => 'required|numeric|exists:properties,id',
            'hosting_capacity' => 'required|numeric|gt:0'

        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }





        if(  session()->exists('orderID') ) {
            $cryptedOrderId =Crypt::encrypt(session()->get('orderID'));
                return  new JsonResponse( [
                    "confirmUrl" => Url::temporarySignedRoute('order.capture',now()->addMinute(2),["orderID"=>$cryptedOrderId]),
                    "orderID" => session()->get('orderID')
                ]);
        }




        $dispnible = json_decode($this->check($request)->getContent()) ;


        if($dispnible->status == "negative")
            return  new JsonResponse($dispnible);




        $property = $propertyRepository?->getById($request->propertyId,['price','check_in_at','check_out_at'])?->first();
        $arrival = new DateTimeImmutable($request->arrival.' '.$property->check_in_at) ;
        $departure= new DateTimeImmutable($request->departure.' '.$property->check_out_at) ;
        $user = auth()->user() ;
        $days = $departure->diff($arrival)->days;
        $price = $days * $property->price ;
       $payment = $this->paypalGateway->createOrder($price,'test');
        if(!$payment['success'])
            return new JsonResponse( [
                "error" => "can't create payment"
            ]) ;

       $reservation = $this->reservationRepository->create(
            [
                "price" => $price,
                "arrival_date" => $arrival ,
                "departure_date" => $departure,
                "status" => "waiting",
                "hosting_capacity"=> $request->hosting_capacity ,
                "ref" => $payment['orderID']
            ]
        );
       $reservation->user()->associate($user);
       $reservation->property()->associate($property);
       $reservation->save();

        $cryptedOrderId = Crypt::encrypt($payment['orderID']) ;

        session()->put("orderID",$payment['orderID']);
        Session::save() ;

       return  new JsonResponse( array_merge( $reservation->only(['price',"ref",'status','id']),[
            "confirmUrl" => Url::temporarySignedRoute('order.capture',now()->addMinute(2),["orderID"=>$cryptedOrderId]),
            "orderID" => $payment['orderID']
        ]));

    }

    public function approvePayment(string $orderID,Request $request)
    {
        $ref = Crypt::decrypt($orderID);
        $reservation = $this->reservationRepository->where('ref',$ref)->first();
        if($reservation->status!='waiting')
            return ['url'=> '/'];

        $capture = $this->paypalGateway->captureOrder($ref);

        $status = $capture->result->status;
        try {
            status:
            switch ($status){
                case 'APPROVED':

                    $capture = $this->paypalGateway->captureOrder($ref);
                    $status=$capture->result->status;
                    $status = $reservation->status = "APPROVED" ;
                    $reservation->save();
                    goto status;
                case 'CREATED':
                    return ["message"=>"created"];
                case 'SAVED':
                    return ["message"=>"saved"];
                case 'VOIDED':
                    return ["message"=>"voided"];
                case 'COMPLETED':
                    $reservation->status = 'payed';
                    $reservation->save();
                    session()->forget("orderID");
                    return ["message"=>"completed"];
                case 'PAYER_ACTION_REQUIRED':
                    return ['url'=>Url::temporarySignedRoute('payment.confirm.error',now()->addSecond(1),['errormessage'=>Crypt::encrypt('unfortunately we don\'t support 3DS authentication for your credit card payment')])];
                default:
                    break;
            }
        }
        catch (\Exception $exception){
            return ["message"=>"error ".$exception->getMessage()];
        }

        return ['message'=> null ];


    }




    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return Response
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
