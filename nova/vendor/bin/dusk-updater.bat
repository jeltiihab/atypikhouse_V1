@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../orchestra/dusk-updater/dusk-updater
php "%BIN_TARGET%" %*
