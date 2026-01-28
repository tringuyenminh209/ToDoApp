@echo off
REM Uses %%LOCALAPPDATA%%\Android\Sdk\platform-tools\adb.exe (default Windows SDK path).
REM For custom path, use setup-port-forwarding.local.bat (gitignored).
set "ADB=%LOCALAPPDATA%\Android\Sdk\platform-tools\adb.exe"
if not exist "%ADB%" set "ADB=%USERPROFILE%\AppData\Local\Android\Sdk\platform-tools\adb.exe"
echo Setting up adb reverse port forwarding...
"%ADB%" reverse tcp:8080 tcp:8080
echo.
echo Port forwarding setup complete!
echo You can now login to the app.
pause
