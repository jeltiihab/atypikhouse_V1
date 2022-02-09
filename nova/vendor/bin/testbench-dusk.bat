@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../orchestra/testbench-dusk/testbench-dusk
php "%BIN_TARGET%" %*
