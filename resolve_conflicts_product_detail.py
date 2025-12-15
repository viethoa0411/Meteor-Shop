import os

file_path = r'e:\laragon\www\meteor\resources\views\client\products\detail.blade.php'

def resolve_conflicts(path):
    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()

    new_lines = []
    in_head = False
    in_origin = False
    
    for line in lines:
        if '<<<<<<< HEAD' in line:
            in_head = True
            in_origin = False
            continue
        elif '=======' in line:
            if in_head:
                in_head = False
                in_origin = True
                continue
        elif '>>>>>>> origin/sua_Bien_The_update' in line:
            if in_origin:
                in_origin = False
                continue
        
        # Logic:
        # - Nếu đang ở HEAD: Bỏ qua (không thêm vào new_lines)
        # - Nếu đang ở Origin: Thêm vào new_lines
        # - Nếu không ở đâu cả: Thêm vào new_lines
        
        if in_head:
            continue
        elif in_origin:
            new_lines.append(line)
        else:
            new_lines.append(line)

    with open(path, 'w', encoding='utf-8') as f:
        f.writelines(new_lines)

if __name__ == '__main__':
    if os.path.exists(file_path):
        resolve_conflicts(file_path)
        print(f"Resolved conflicts in {file_path}")
    else:
        print(f"File not found: {file_path}")
