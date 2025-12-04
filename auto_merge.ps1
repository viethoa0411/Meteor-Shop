# Script tự động merge main vào nhánh hiện tại
$env:GIT_EDITOR = "true"

# Kiểm tra xem có đang trong quá trình merge không
if (Test-Path .git/MERGE_HEAD) {
    Write-Host "Đang hoàn tất merge..."
    git commit --no-edit
} else {
    Write-Host "Bắt đầu merge origin/main..."
    git merge origin/main -m "merge main into feature/dashboard-order-statistics-improved"
}

Write-Host "Merge hoàn tất!"
git status























