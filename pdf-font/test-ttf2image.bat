for %%F in ("%CD%\*.ttf") do ttf2img.exe -o "%%~nF-" "%%F"

pause
