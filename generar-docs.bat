@echo off
echo Generando documentacion DATAFILLER...
cd docs
docfx docfx.json
echo.
echo Documentacion generada en: docs\_site\index.html
echo.
echo Abrir en navegador? (s/n)
set /p respuesta=
if /i "%respuesta%"=="s" start "" "_site\index.html"
pause