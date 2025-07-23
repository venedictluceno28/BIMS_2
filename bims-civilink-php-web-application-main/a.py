import os

# Use current directory
css_dir = os.getcwd()

# Font size CSS to append
font_size_css = """
/* FONT SIZE START */
body.font-small, html.font-small {
    font-size: 12px !important;
}
body.font-medium, html.font-medium {
    font-size: 16px !important;
}
body.font-large, html.font-large {
    font-size: 20px !important;
}
/* FONT SIZE END */
"""

# Loop through all .css files in current directory and subfolders
for root, dirs, files in os.walk(css_dir):
    for file in files:
        if file.endswith(".css"):
            css_path = os.path.join(root, file)
            # Append font size CSS to the CSS file
            with open(css_path, 'a', encoding='utf-8') as f:
                f.write(font_size_css)
                print(f"[UPDATED] Font size CSS appended in: {file}")
