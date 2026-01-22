import os
import re

def remove_comments(content, file_extension):
    if file_extension in ['.php', '.js', '.css', '.scss', '.swift']:
        # Remove single line comments // ... but NOT http://
        content = re.sub(r'(?<!:)\/\/.*', '', content)
        # Remove multi-line comments /* ... */
        content = re.sub(r'\/\*[\s\S]*?\*\/', '', content, flags=re.DOTALL)
        if file_extension == '.php':
             # Remove # comments
             content = re.sub(r'#.*', '', content)
    
    if file_extension == '.blade.php':
        # Remove Blade comments {{-- --}}
        content = re.sub(r'\{\{--[\s\S]*?--\}\}', '', content, flags=re.DOTALL)
        # Remove HTML comments <!-- -->
        content = re.sub(r'<!--[\s\S]*?-->', '', content, flags=re.DOTALL)

    return content

def clean_directory(root_dir):
    extensions = ['.php', '.js', '.css', '.blade.php', '.swift']
    skip_dirs = ['vendor', 'node_modules', '.git', 'storage', 'tools', 'Pods']
    
    for subdir, dirs, files in os.walk(root_dir):
        # Skip blacklisted directories
        dirs[:] = [d for d in dirs if d not in skip_dirs]
        
        for file in files:
            if any(file.endswith(ext) for ext in extensions):
                file_path = os.path.join(subdir, file)
                
                # Special skip for this script itself
                if 'clean_project.py' in file_path:
                    continue

                try:
                    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                        content = f.read()
                    
                    # Determine extension
                    ext = ''
                    if file.endswith('.blade.php'):
                        ext = '.blade.php'
                    else:
                        ext = os.path.splitext(file)[1]
                        
                    new_content = remove_comments(content, ext)
                    
                    # Remove empty lines
                    lines = [line for line in new_content.splitlines() if line.strip()]
                    cleaned_content = '\n'.join(lines)
                    
                    if cleaned_content != content:
                        with open(file_path, 'w', encoding='utf-8') as f:
                            f.write(cleaned_content)
                        print(f"Cleaned: {file_path}")
                except Exception as e:
                    print(f"Skipped {file_path}: {e}")

if __name__ == "__main__":
    # Target all project folders
    target_dirs = [
        'app',
        'config',
        'database',
        'public',
        'resources',
        'routes',
        'Iphone',
        'mobile-app/src'
    ]
    
    base_path = os.getcwd()
    for d in target_dirs:
        full_path = os.path.join(base_path, d)
        if os.path.exists(full_path):
            print(f"Processing directory: {full_path}")
            clean_directory(full_path)
