
import re

def check_html_balance(file_path):
    with open(file_path, 'r') as f:
        content = f.read()
    
    # Simple regex to find tags
    # This won't handle comments or strings perfectly, but it's a start
    tags = re.findall(r'<(/?)([a-zA-Z0-9:-]+)([^>]*)>', content)
    
    stack = []
    for is_closing, tag_name, attrs in tags:
        if tag_name in ['img', 'br', 'hr', 'input', 'link', 'meta']:
            continue
        if is_closing:
            if not stack:
                print(f"Error: Closing tag </{tag_name}> found with no opening tag.")
                continue
            last_tag = stack.pop()
            if last_tag != tag_name:
                print(f"Error: Mismatched tag. Expected </{last_tag}>, found </{tag_name}>.")
        else:
            if attrs.endswith('/'):
                continue
            stack.append(tag_name)
    
    if stack:
        print(f"Error: Unclosed tags: {stack}")
    else:
        print("All tags seem balanced (ignoring script/style content and edge cases).")

check_html_balance('/home/daha/DEV/DAZO/PROJECT/resources/js/views/public/PublicFront.vue')
