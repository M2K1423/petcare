const fs = require('fs');
const path = require('path');

const files = [
    'admin-appointments.vue',
    'admin-dashboard.vue',
    'admin-doctors.vue',
    'admin-inventory.vue',
    'admin-logs.vue',
    'admin-pets.vue',
    'admin-reports.vue',
    'admin-services.vue',
    'admin-settings.vue',
    'admin-users.vue'
];

const dir = path.join(__dirname, '../resources/js/pages-vue');

for (const file of files) {
    const filePath = path.join(dir, file);
    let content = fs.readFileSync(filePath, 'utf8');

    // 1. Add import
    if (!content.includes('LoadingSpinner.vue')) {
        content = content.replace(
            /(import {[^}]+} from 'vue';\n)/,
            "$1import LoadingSpinner from '../components/LoadingSpinner.vue';\n"
        );
        if (!content.includes('LoadingSpinner')) {
            content = content.replace(
                /<script setup>\n/,
                "<script setup>\nimport LoadingSpinner from '../components/LoadingSpinner.vue';\n"
            );
        }
    }

    // 2. Add isLoading state
    if (!content.includes('const isLoading = ref(true)')) {
        const fetchRegex = /const (fetch[A-Za-z]+|generateReport) = async/;
        content = content.replace(fetchRegex, "const isLoading = ref(true);\n\nconst $1 = async");
    }

    // 3. Add isLoading toggle in fetch function
    const funcMatch = content.match(/const (fetch[A-Za-z]+|generateReport) = async \([^)]*\) => {([\s\S]*?)(};|\n}\n)/);
    if (funcMatch) {
        let funcBody = funcMatch[2];
        if (!funcBody.includes('isLoading.value = true')) {
            // Find the try block
            const tryMatch = funcBody.match(/(\s*try\s*{)/);
            if (tryMatch) {
                // Add to start of function
                funcBody = funcBody.replace(tryMatch[1], "\n  isLoading.value = true;" + tryMatch[1]);
                
                // Add finally block
                if (!funcBody.includes('finally {')) {
                    const catchMatch = funcBody.match(/catch\s*\([^)]*\)\s*{[\s\S]*?}(\s*)$/);
                    if (catchMatch) {
                        funcBody = funcBody.replace(/catch\s*\([^)]*\)\s*{[\s\S]*?}(\s*)$/, (match) => {
                            return match.replace(/}(\s*)$/, "} finally {\n    isLoading.value = false;\n  }$1");
                        });
                    }
                }
                
                content = content.replace(funcMatch[0], `const ${funcMatch[1]} = async () => {${funcBody}${funcMatch[3]}`);
            }
        }
    }

    // 4. Wrap template
    if (!content.includes('<LoadingSpinner v-else />')) {
        if (content.includes('<div class="space-y-6">')) {
            content = content.replace(
                /<template>\n  <div class="space-y-6">\n/,
                "<template>\n  <div class=\"space-y-6\">\n    <template v-if=\"!isLoading\">\n"
            );
            
            // This is tricky, we need to replace the last </div> before </template>
            content = content.replace(
                /    <\/div>\n  <\/div>\n<\/template>/,
                "    </div>\n    </template>\n    <LoadingSpinner v-else />\n  </div>\n</template>"
            );
            
            // Sometimes it's just </div>\n</template> if there wasn't a wrapper inside
            if (!content.includes('</template>\n    <LoadingSpinner')) {
               content = content.replace(
                    /  <\/div>\n<\/template>/,
                    "    </template>\n    <LoadingSpinner v-else />\n  </div>\n</template>"
                );
            }
        } else if (content.includes('<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">')) {
            // Special for admin-dashboard
            content = content.replace(
                /<template>\n  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">/,
                "<template>\n  <template v-if=\"!isLoading\">\n  <div class=\"grid grid-cols-1 md:grid-cols-4 gap-4 mb-8\">"
            );
            content = content.replace(
                /<\/div>\n<\/template>/,
                "</div>\n  </template>\n  <LoadingSpinner v-else />\n</template>"
            );
        }
    }

    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated ${file}`);
}
