@echo off
cd /d C:\laragon\www\Meteor-Shop
git -c core.editor=true commit --no-edit
if %errorlevel% equ 0 (
    echo Merge thanh cong!
    git status
) else (
    echo Co loi xay ra
    git status
)
pause























