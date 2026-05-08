@echo off
REM Start ngrok tunnel for local VNPay testing
REM Keep this window open to maintain the tunnel

"%LOCALAPPDATA%\Microsoft\WinGet\Links\ngrok.exe" http 80

pause
