@ECHO off
@CHOICE /M "Update composer"
IF ERRORLEVEL 2 GOTO libs_ask
IF ERRORLEVEL 1 GOTO composer_upd
GOTO end

:composer_upd
CALL composer clearcache
CALL composer self-update
CALL composer update
GOTO libs_ask

:libs_ask
@ECHO off
@CHOICE /M "Update js/css libs"
IF ERRORLEVEL 2 GOTO end
IF ERRORLEVEL 1 GOTO libs_upd
GOTO end

:libs_upd
CALL npm install
CALL bower install
CALL gulp create-vendor-js
CALL gulp create-vendor-css
CALL gulp create-vendor-assets
GOTO result_ask

:result_ask
@ECHO off
@CHOICE /M "Create result files?"
IF ERRORLEVEL 2 GOTO libs_del_ask
IF ERRORLEVEL 1 GOTO result
GOTO end

:result
CALL gulp create-result-js
CALL gulp create-result-css
GOTO libs_del_ask

:libs_del_ask
@ECHO off
@CHOICE /M "Delete libs folders"
IF ERRORLEVEL 2 GOTO end
IF ERRORLEVEL 1 GOTO libs_del
GOTO end

:libs_del
RMDIR "bower_components" /S/Q
RMDIR "node_modules" /S/Q
GOTO end

:end
@PAUSE