<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Category;
use App\Models\Equipement;
use App\Models\Property;
use App\Repositories\PropertyRepository;
use App\Repositories\ReservationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PropertyController extends Controller
{

    /**
     * @var PropertyRepository
     */
    private $propertyRepository;
    /**
     * @var ReservationRepository
     */
    private $reservationRepository;

    /**
     * @param PropertyRepository $propertyRepository
     * @param ReservationRepository $reservationRepository
     */
    public function __construct(PropertyRepository $propertyRepository, ReservationRepository $reservationRepository)
    {
        $this->propertyRepository = $propertyRepository;
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function propertyThreeLast(): JsonResponse
    {
        $propertyThreeLast = $this->propertyRepository->with('address')->orderBy('id', 'desc')->limit(3)->get();
        return  new JsonResponse($propertyThreeLast);
    }


    public function propertiesLimit(): JsonResponse
    {
        $property = $this->propertyRepository->with('address')->orderBy('id', 'desc')->limit(50)->get();
        return  new JsonResponse($property);
    }

    public function propertiesByUser(): JsonResponse
    {
        $user = auth()->user();
        $property = $this->propertyRepository->where("user_id", $user?->id)->get();
        return  new JsonResponse($property);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'arrival' => 'required|date|after:today',
            'departure' => 'required|date|after:arrival',
            'destination' => 'required|string|exists:addresses,city',
            'maxTraveler' => 'required|numeric|gte:0',

        ]);


        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $arrival = $request->arrival ;
        $departure = $request->departure ;


        $destination = $request->destination ;
        $maxTraveler = $request->maxTraveler;

        $propertiesResult = $this->propertyRepository->findPropertiesBySearchCity($maxTraveler,$destination);
        $finalResult = [];
        $i = 0;
        foreach ($propertiesResult as $proprety) {
            $reservationResult = $this->reservationRepository->findReservationByPropretyId($proprety->id,$arrival);
            $disponibles = [];
            if(($c=count($reservationResult))>0){

                for($j = 0;$j <= $c -2 ;$j++ ) {

                    if(( $daycount =  $reservationResult[$j]->departure_date->diff($reservationResult[$j+1]?->arrival_date)->format('%r%a')) >0){

                        $disponibles[$j] = [
                            "day"=>$daycount,
                            "from"=>$reservationResult[$j]->departure_date->format('Y-m-d'),
                            "to" =>$reservationResult[$j+1]->arrival_date->format('Y-m-d')
                        ] ;
                    }

                }

                // in this case there is only one reservation
                $disponibles[$j = $c -1] = [
                    "day"=>"*",
                    "from"=> $reservationResult[$j]->departure_date->format('Y-m-d'),
                    "to" => "*"
                ];
            }
            else {
                $disponibles[0] = [
                    "day"=>"*",
                    "from"=>"*",
                    "to"=> "*"
                ];
            }
            $finalResult[$i++]  = [
                "id" => $proprety->id,
                "title" => $proprety->name,
                "address" => $proprety->address,
                "price" => $proprety->price,
                "hostingcapacity" => $proprety->hosting_capacity,
                'disponilibilite' => $disponibles
            ];
        }
        return  new JsonResponse($finalResult);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[

            //validation properties
            'category' => 'required|numeric|exists:categories,id',
            'property_name' => 'required|string',
            'rooms' => 'required|numeric',
            'description' => 'required|min:3|max:1000',
            'surface' => 'required|string',
            'hosting_capacity' => 'required|numeric',
            'checkin_at' => 'required|date_format:H:i',
            'checkout_at' => 'required|date_format:H:i',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            //'images'
            'images' => 'required|array|min:5|max:5',
            'images.*' => 'mimes:jpeg,jpg,png|max:2048',


            //validation addresses
            'street' => 'required|string',
            'street_number' => 'required|numeric',
            'postal_code' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'region' => 'required|string',
        ]);


        if($validator->fails()){
            return response()->json($validator->errors());
        }

        //Equipements
        $dataEquipments = [];
        $category = Category::with('equipements')->where("id", $request->category)->first();
        $equipements = $category?->equipements;
        if ( $request->has('equipement') && count($equipements) > 0 ) {
            $equipementForm = $request->input('equipement');
            foreach ($equipements as $equipement) {
                if(array_key_exists($equipement->id, $equipementForm)) {
                    $dataEquipments[$equipement->name] = "1";
                } else {
                    $dataEquipments[$equipement->name] = "0";
                }
            }

        }

        //Attributes
        $dataAttributes = [];
        $category = Category::with('attributes')->where("id", $request->category)->first();
        $attributes = $category?->attributes;
        //var_dump($attributes);
        if ( $request->has('attributes') && count($attributes) > 0 ) {
            $attributesForm = $request->input('attributes');
            foreach ($attributes as $attribute) {
                if($attribute?->type == "boolean") {
                    if(array_key_exists($attribute->id, $attributesForm)) {
                        $dataAttributes[$attribute->name] = "1";
                    } else {
                        $dataAttributes[$attribute->name] = "0";
                    }
                } else {
                    $dataAttributes[$attribute->name] = $attributesForm[$attribute->id];
                }
            }

        }

        //Images
        $data = [];
        if($request->hasfile('images'))
        {
            //var_dump($request->file('images'));
            $i = 0;
            foreach($request->file('images') as $file)
            {
                $uniqid = uniqid();
                $rand_start = rand(1,5);
                $rand_8_char = substr($uniqid,$rand_start,8);
                $name = time().$rand_8_char.'.'.$file->extension();
                $file->move(public_path().'/uploads/', $name);
                $data["img".$i] = $name;
                $i++;
            }
        }


        $property = Property::create([
            "user_id" => auth()->user()->id,
            "category_id" => $request->category,
            "name" => $request->property_name,
            "rooms" => $request->rooms,
            "description" => $request->description,
            "surface" => $request->surface,
            "hosting_capacity" => $request->hosting_capacity,
            "equipments" => json_encode(count($dataEquipments)>0?$dataEquipments : (object) array(), true),
            "dynamic_attributes" => json_encode(count($dataAttributes)>0?$dataAttributes : (object) array(), true),
            "check_in_at" => $request->checkin_at,
            "check_out_at" => $request->checkout_at,
            //"rate" => $request->category,
            //"reviews" => $request->category,
            "price" => $request->price,
            "is_activated" => 0,
            "images" => json_encode($data ?? "", true)
        ]);

        Address::create([
            "property_id" => $property->id,
            "street" => $request->street,
            "street_number" => $request->street_number,
            "postal_code" => $request->postal_code,
            "city" => $request->city,
            "country" => $request->country,
            "region" => $request->region,
        ]);



        return  new JsonResponse([
            "status" => "saccess",
        ],200);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Property $property
     * @return JsonResponse
     */
    public function show(Property $property): JsonResponse
    {
        $property = $this->propertyRepository->with('address', 'user', 'category', 'category.equipements')->getById($property->id);
        return new JsonResponse($property);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Property $property): \Illuminate\Http\Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Property $property
     * @return \Illuminate\Http\Response
     */
    public function destroy(Property $property)
    {
        //
    }
}
