@echo off
echo Setting up adb reverse port forwarding...
C:\Users\2240788\AppData\Local\Android\Sdk\platform-tools\adb.exe reverse tcp:8080 tcp:8080
echo.
echo Port forwarding setup complete!
echo You can now login to the app.
pause
