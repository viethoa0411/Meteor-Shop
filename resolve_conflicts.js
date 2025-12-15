const fs = require('fs');
const path = 'e:\\laragon\\www\\meteor\\resources\\views\\client\\products\\detail.blade.php';

try {
    const content = fs.readFileSync(path, 'utf8');
    const lines = content.split('\n');
    let newLines = [];
    let inHead = false;
    let inOrigin = false;

    for (let line of lines) {
        if (line.includes('<<<<<<< HEAD')) {
            inHead = true;
            inOrigin = false;
            continue;
        } else if (line.includes('=======')) {
            if (inHead) {
                inHead = false;
                inOrigin = true;
                continue;
            }
        } else if (line.includes('>>>>>>> origin/sua_Bien_The_update')) {
            if (inOrigin) {
                inOrigin = false;
                continue;
            }
        }

        if (inHead) {
            continue;
        } else if (inOrigin) {
            newLines.push(line);
        } else {
            newLines.push(line);
        }
    }

    fs.writeFileSync(path, newLines.join('\n'), 'utf8');
    console.log(`Resolved conflicts in ${path}`);
} catch (err) {
    console.error(err);
}
