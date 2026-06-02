@echo off
REM Start ngrok tunnel for local VNPay testing
REM Keep this window open to maintain the tunnel

"%LOCALAPPDATA%\Microsoft\WinGet\Links\ngrok.exe" http --domain=central-gazelle-honest.ngrok-free.app 8000

pause
