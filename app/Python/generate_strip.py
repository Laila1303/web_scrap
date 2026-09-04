import sys
import os
import math
from PIL import Image, ImageDraw, ImageFont

def draw_y2k_star(draw, cx, cy, r, color):
    draw.polygon([
        (cx, cy - r), 
        (cx + int(r*0.25), cy - int(r*0.25)),
        (cx + r, cy),
        (cx + int(r*0.25), cy + int(r*0.25)),
        (cx, cy + r),
        (cx - int(r*0.25), cy + int(r*0.25)),
        (cx - r, cy),
        (cx - int(r*0.25), cy - int(r*0.25))
    ], fill=color)

def draw_ticket_notches(draw, card_x1, card_y1, card_x2, card_y2, bg_color):
    notch_r = 15
    mid_y = (card_y1 + card_y2) // 2
    # Left notch
    draw.pieslice(
        [(card_x1 - notch_r, mid_y - notch_r), (card_x1 + notch_r, mid_y + notch_r)],
        270, 90, fill=bg_color
    )
    # Right notch
    draw.pieslice(
        [(card_x2 - notch_r, mid_y - notch_r), (card_x2 + notch_r, mid_y + notch_r)],
        90, 270, fill=bg_color
    )

def draw_bow_ribbon(draw, cx, cy, color):
    # cx, cy is the center of the bow
    # Central loop/knot
    draw.ellipse([(cx - 7, cy - 7), (cx + 7, cy + 7)], fill=color)
    # Left loop
    draw.polygon([(cx - 7, cy), (cx - 22, cy - 12), (cx - 26, cy - 6), (cx - 22, cy + 6), (cx - 7, cy)], fill=color)
    # Right loop
    draw.polygon([(cx + 7, cy), (cx + 22, cy - 12), (cx + 26, cy - 6), (cx + 22, cy + 6), (cx + 7, cy)], fill=color)
    # Left tail
    draw.polygon([(cx - 5, cy + 3), (cx - 18, cy + 22), (cx - 13, cy + 24), (cx - 3, cy + 7)], fill=color)
    # Right tail
    draw.polygon([(cx + 5, cy + 3), (cx + 18, cy + 22), (cx + 13, cy + 24), (cx + 3, cy + 7)], fill=color)

def generate_strip(img1_path, img2_path, img3_path, out_path, header_text, footer_text, style_theme, photo_shape):
    paths = [img1_path, img2_path, img3_path]
    for p in paths:
        if not os.path.exists(p):
            print(f"Error: File {p} not found.")
            return False

    # Setup Strip Canvas
    strip_w = 600
    photo_w = 440
    photo_h = 300 if photo_shape == "oval" else 330
    
    top_pad = 200
    gap = 25
    bot_pad = 220
    
    strip_h = top_pad + (3 * photo_h) + (2 * gap) + bot_pad

    # Determine Project Root for assets
    project_root = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

    # 1. LOAD BACKGROUND IMAGE / SOLID COLOR
    canvas = None
    if style_theme == "ppg_collage":
        bg_img_path = os.path.join(project_root, "public", "images", "ppg_bg_collage.jpg")
        if os.path.exists(bg_img_path):
            bg_img = Image.open(bg_img_path).convert("RGBA")
            # Crop/Resize to fit strip size
            canvas = bg_img.resize((strip_w, strip_h), Image.Resampling.LANCZOS)
    elif style_theme == "denim_y2k":
        # Create a beautiful custom denim background layout with textures
        bg_color = (20, 35, 60, 255) if photo_shape == "square" else (76, 110, 141, 255) # Dark denim for D4, light denim for D3
        bg_img_path = os.path.join(project_root, "public", "images", "denim_bg_collage.png")
        if os.path.exists(bg_img_path):
            bg_img = Image.open(bg_img_path).convert("RGBA")
            canvas = bg_img.resize((strip_w, strip_h), Image.Resampling.LANCZOS)
        else:
            canvas = Image.new("RGBA", (strip_w, strip_h), bg_color)
            
        # Draw D3 / D4 lace overlays on top of the denim canvas
        draw = ImageDraw.Draw(canvas)
        
        card_x1, card_y1, card_x2, card_y2 = 45, 50, strip_w - 45, strip_h - 50
        
        # Draw main ticket card
        draw.rounded_rectangle([(card_x1, card_y1), (card_x2, card_y2)], radius=25, fill=(252, 248, 238, 255))
        
        # Double border lines (crimson/burgundy ticket color)
        draw.rounded_rectangle([(card_x1, card_y1), (card_x2, card_y2)], radius=25, outline=(139, 45, 45, 255), width=3)
        draw.rounded_rectangle([(card_x1 + 6, card_y1 + 6), (card_x2 - 6, card_y2 - 6)], radius=20, outline=(139, 45, 45, 255), width=1)
        
        # Circular ticket notches and dashed separators
        denim_blue = (50, 80, 120, 255) # Match denim color
        notch_r = 16
        div_y1 = 175
        div_y2 = strip_h - 175
        
        for ny in [div_y1, div_y2]:
            draw.ellipse([(card_x1 - notch_r, ny - notch_r), (card_x1 + notch_r, ny + notch_r)], fill=denim_blue)
            draw.ellipse([(card_x2 - notch_r, ny - notch_r), (card_x2 + notch_r, ny + notch_r)], fill=denim_blue)
            # Dashed line
            for lx in range(card_x1 + 16, card_x2 - 16, 12):
                draw.line([(lx, ny), (lx + 6, ny)], fill=(139, 45, 45, 255), width=2)
                
        # Draw ticket header typography details inside the header area
        font_ticket_script = None
        font_ticket_bold = None
        try:
            for fp in ["C:\\Windows\\Fonts\\georgiai.ttf", "C:\\Windows\\Fonts\\timesi.ttf", "C:\\Windows\\Fonts\\ariali.ttf"]:
                if os.path.exists(fp):
                    font_ticket_script = ImageFont.truetype(fp, 32)
                    break
            for fp in ["C:\\Windows\\Fonts\\georgiab.ttf", "C:\\Windows\\Fonts\\times.ttf", "C:\\Windows\\Fonts\\arial.ttf"]:
                if os.path.exists(fp):
                    font_ticket_bold = ImageFont.truetype(fp, 17)
                    break
        except Exception:
            pass
            
        if not font_ticket_script:
            font_ticket_script = ImageFont.load_default()
        if not font_ticket_bold:
            font_ticket_bold = ImageFont.load_default()
            
        draw.text((strip_w // 2, 95), "Movie Theatre", fill=(139, 45, 45, 255), font=font_ticket_script, anchor="mm")
        
        sub_y = 145
        draw.text((130, sub_y), "15 c", fill=(139, 45, 45, 255), font=font_ticket_bold, anchor="mm")
        draw.text((strip_w // 2, sub_y), "ADMIT ONE", fill=(139, 45, 45, 255), font=font_ticket_bold, anchor="mm")
        draw.text((strip_w - 130, sub_y), "ONE DAY", fill=(139, 45, 45, 255), font=font_ticket_bold, anchor="mm")
        
        # Divider bars inside header sub-label
        draw.line([(200, sub_y - 20), (200, sub_y + 20)], fill=(139, 45, 45, 255), width=2)
        draw.line([(400, sub_y - 20), (400, sub_y + 20)], fill=(139, 45, 45, 255), width=2)
    elif style_theme == "polaroid_printer":
        bg_img_path = os.path.join(project_root, "public", "images", "polaroid_bg_collage.jpg")
        if os.path.exists(bg_img_path):
            bg_img = Image.open(bg_img_path).convert("RGBA")
            canvas = bg_img.resize((strip_w, strip_h), Image.Resampling.LANCZOS)

    # Fallback to solid color if background images don't exist
    if canvas is None:
        bg_color = (244, 239, 230, 255)
        if style_theme == "classic_vintage":
            bg_color = (43, 27, 16, 255)
        elif style_theme == "denim_y2k":
            bg_color = (76, 110, 141, 255)
        elif style_theme == "ppg_collage":
            bg_color = (255, 192, 203, 255)
        elif style_theme == "polaroid_printer":
            bg_color = (139, 90, 43, 255)
        canvas = Image.new("RGBA", (strip_w, strip_h), bg_color)

    draw = ImageDraw.Draw(canvas)

    # 2. DRAW BASE CARD / GRID WRAPPER
    if style_theme == "classic_vintage":
        # Classic vintage ticket card shape
        card_x1, card_y1, card_x2, card_y2 = 50, 120, strip_w - 50, strip_h - 120
        draw.rounded_rectangle([(card_x1, card_y1), (card_x2, card_y2)], radius=25, fill=(253, 252, 248, 255))
        draw_ticket_notches(draw, card_x1, card_y1, card_x2, card_y2, (43, 27, 16, 255))
        
    elif style_theme == "ppg_collage":
        # Draw a semi-transparent white card in the center to highlight photos
        card_x1, card_y1, card_x2, card_y2 = 50, 130, strip_w - 50, strip_h - 130
        # Draw ticket shape card with 85% opacity white
        overlay = Image.new("RGBA", (strip_w, strip_h), (0,0,0,0))
        overlay_draw = ImageDraw.Draw(overlay)
        overlay_draw.rounded_rectangle([(card_x1, card_y1), (card_x2, card_y2)], radius=25, fill=(253, 252, 248, 220))
        # Draw ticket notches on overlay
        # Since ppg collage has background image, notches should be filled with transparent/see-through or background colors.
        # For simplicity, we just keep it as a nice rounded rectangle overlay
        canvas.paste(overlay, (0,0), overlay)

    # Paste Photos
    current_y = top_pad
    for i, path in enumerate(paths):
        try:
            img = Image.open(path).convert("RGBA")
            
            # Crop to aspect ratio
            img_w, img_h = img.size
            target_ratio = photo_w / photo_h
            current_ratio = img_w / img_h
            
            if current_ratio > target_ratio:
                new_w = int(img_h * target_ratio)
                left = (img_w - new_w) // 2
                img = img.crop((left, 0, left + new_w, img_h))
            else:
                new_h = int(img_w / target_ratio)
                top = (img_h - new_h) // 2
                img = img.crop((0, top, img_w, top + new_h))
                
            img = img.resize((photo_w, photo_h), Image.Resampling.LANCZOS)

            # Masking for Oval Shape
            if photo_shape == "oval":
                mask = Image.new("L", (photo_w, photo_h), 0)
                mask_draw = ImageDraw.Draw(mask)
                mask_draw.ellipse([(0, 0), (photo_w, photo_h)], fill=255)
                output_img = Image.new("RGBA", (photo_w, photo_h), (0,0,0,0))
                output_img.paste(img, (0,0), mask)
                img = output_img
            elif style_theme == "denim_y2k" and photo_shape == "square":
                # Mask with rounded corners for denim_y2k square layout (D4)
                mask = Image.new("L", (photo_w, photo_h), 0)
                mask_draw = ImageDraw.Draw(mask)
                mask_draw.rounded_rectangle([(0, 0), (photo_w, photo_h)], radius=20, fill=255)
                output_img = Image.new("RGBA", (photo_w, photo_h), (0,0,0,0))
                output_img.paste(img, (0,0), mask)
                img = output_img

            paste_x = (strip_w - photo_w) // 2
            
            # Draw frame backing per style (no backing or film strip holes for denim_y2k)
            if style_theme == "polaroid_printer":
                frame_w, frame_h = photo_w + 30, photo_h + 20
                frame_x = (strip_w - frame_w) // 2
                draw.rectangle([(frame_x, current_y - 10), (frame_x + frame_w, current_y + photo_h + 10)], fill=(255, 255, 255, 255), outline=(0,0,0,255), width=2)

            canvas.paste(img, (paste_x, current_y), img)

            # Draw border around photo slot
            border_color = (92, 64, 51, 255) if style_theme in ["classic_vintage", "polaroid_printer", "ppg_collage"] else (255, 255, 255, 255)
            if style_theme == "denim_y2k":
                border_color = (139, 45, 45, 255)

            if photo_shape == "oval":
                draw.ellipse(
                    [(paste_x, current_y), (paste_x + photo_w, current_y + photo_h)],
                    outline=border_color,
                    width=4
                )
            else:
                if style_theme == "denim_y2k":
                    draw.rounded_rectangle(
                        [(paste_x, current_y), (paste_x + photo_w, current_y + photo_h)],
                        radius=20,
                        outline=border_color,
                        width=4
                    )
                else:
                    draw.rectangle(
                        [(paste_x, current_y), (paste_x + photo_w, current_y + photo_h)],
                        outline=border_color,
                        width=3
                    )

            current_y += photo_h + gap
        except Exception as e:
            print(f"Error processing image {i}: {e}")
            return False

    # 3. TEXTS AND DECORATIVE STICKERS
    font_header = None
    font_footer = None
    try:
        font_paths = [
            "C:\\Windows\\Fonts\\georgiab.ttf",
            "C:\\Windows\\Fonts\\times.ttf",
            "C:\\Windows\\Fonts\\arial.ttf"
        ]
        for fp in font_paths:
            if os.path.exists(fp):
                font_header = ImageFont.truetype(fp, 36)
                font_footer = ImageFont.truetype(fp, 20)
                break
    except Exception:
        pass

    if not font_header:
        font_header = ImageFont.load_default()
        font_footer = ImageFont.load_default()

    # Draw a cute sticker-label banner frame for the Header (except for denim_y2k which has its own ticket header integrated)
    if style_theme != "denim_y2k":
        banner_w = 440
        banner_h = 70
        bx1 = (strip_w - banner_w) // 2
        by1 = 35
        bx2 = bx1 + banner_w
        by2 = by1 + banner_h
        
        # Header label coloring (cream badge with espresso outline)
        draw.rounded_rectangle(
            [(bx1, by1), (bx2, by2)],
            radius=15,
            fill=(253, 252, 248, 245), # Creamy background
            outline=(92, 64, 51, 255),  # Espresso border
            width=3
        )

        # Draw Header text inside the banner
        draw.text((strip_w // 2, 70), header_text, fill=(92, 64, 51, 255), font=font_header, anchor="mm")

    # Draw Footer elements
    if style_theme == "classic_vintage":
        # Draw barcode
        barcode_y = card_y2 - 80
        barcode_x = card_x1 + 60
        barcode_w = (card_x2 - card_x1) - 120
        import random
        random.seed(42)
        curr_bx = barcode_x
        while curr_bx < barcode_x + barcode_w:
            line_w = random.choice([2, 4, 6, 8])
            draw.rectangle([(curr_bx, barcode_y), (curr_bx + line_w, barcode_y + 40)], fill=(92, 64, 51, 255))
            curr_bx += line_w + random.choice([2, 4, 6])
        draw.text((strip_w // 2, card_y2 - 25), footer_text, fill=(92, 64, 51, 255), font=font_footer, anchor="mm")
        
        # Clip
        draw.rounded_rectangle([(strip_w//2 - 30, card_y1 - 20), (strip_w//2 + 30, card_y1 + 10)], radius=8, fill=(192, 192, 192, 255), outline=(92, 64, 51, 255), width=2)
        draw.ellipse([(strip_w//2 - 10, card_y1 - 10), (strip_w//2 + 10, card_y1 + 10)], fill=(128, 128, 128, 255))

    elif style_theme == "denim_y2k":
        # Draw ticket-themed footer details matching reference image
        # Draw Barcode on the bottom right of the ticket
        bx = card_x2 - 100
        by = strip_h - 145
        import random
        random.seed(99)
        for _ in range(12):
            w = random.choice([2, 4, 6])
            draw.rectangle([(bx, by), (bx + w, by + 65)], fill=(139, 45, 45, 255))
            bx += w + random.choice([2, 4])
            
        # Write ticket footer labels
        # Custom Ticket script/bold font configurations
        font_ticket_script = None
        font_ticket_bold = None
        font_ticket_small = None
        try:
            for fp in ["C:\\Windows\\Fonts\\georgiai.ttf", "C:\\Windows\\Fonts\\timesi.ttf", "C:\\Windows\\Fonts\\ariali.ttf"]:
                if os.path.exists(fp):
                    font_ticket_script = ImageFont.truetype(fp, 32)
                    break
            for fp in ["C:\\Windows\\Fonts\\georgiab.ttf", "C:\\Windows\\Fonts\\times.ttf", "C:\\Windows\\Fonts\\arial.ttf"]:
                if os.path.exists(fp):
                    font_ticket_bold = ImageFont.truetype(fp, 26)
                    font_ticket_small = ImageFont.truetype(fp, 14)
                    break
        except Exception:
            pass
            
        if not font_ticket_script:
            font_ticket_script = ImageFont.load_default()
        if not font_ticket_bold:
            font_ticket_bold = ImageFont.load_default()
        if not font_ticket_small:
            font_ticket_small = ImageFont.load_default()

        draw.text((80, strip_h - 140), "HEY,", fill=(139, 45, 45, 255), font=font_ticket_script, anchor="la")
        draw.text((80, strip_h - 110), "GORGEOUS", fill=(139, 45, 45, 255), font=font_ticket_bold, anchor="la")
        
        # Display the custom footer text next to a location pin
        draw.text((80, strip_h - 78), f"📍 {footer_text}", fill=(139, 45, 45, 255), font=font_ticket_small, anchor="la")
        
        # Draw a cute flower sticker on the left edge between photo 1 and 2
        flower_path = os.path.join(project_root, "public", "images", "flower_sticker_1.png")
        if os.path.exists(flower_path):
            try:
                flower = Image.open(flower_path).convert("RGBA")
                flower.thumbnail((70, 70), Image.Resampling.LANCZOS)
                canvas.paste(flower, (15, 600), flower)
            except Exception as e:
                print(f"Error loading flower sticker: {e}")
                
        # Draw a cute tilted "ADMIT ONE" ticket sticker on the right edge
        try:
            ticket_overlay = Image.new("RGBA", (150, 60), (0,0,0,0))
            to_draw = ImageDraw.Draw(ticket_overlay)
            to_draw.rounded_rectangle([(0, 0), (150, 60)], radius=10, fill=(255, 180, 190, 255), outline=(139, 45, 45, 255), width=2)
            to_draw.text((75, 30), "ADMIT ONE", fill=(139, 45, 45, 255), font=font_ticket_small, anchor="mm")
            rotated_ticket = ticket_overlay.rotate(15, expand=True, resample=Image.Resampling.BICUBIC)
            canvas.paste(rotated_ticket, (strip_w - 120, 480), rotated_ticket)
        except Exception as e:
            print(f"Error drawing ticket overlay: {e}")

    elif style_theme == "ppg_collage":
        draw.text((strip_w // 2, card_y2 - 25), footer_text, fill=(92, 64, 51, 255), font=font_footer, anchor="mm")
        
        # Load Powerpuff Girls stickers from individual clean PNG assets
        blossom_path = os.path.join(project_root, "public", "images", "blossom_sticker.png")
        bubbles_path = os.path.join(project_root, "public", "images", "bubbles_sticker.png")
        buttercup_path = os.path.join(project_root, "public", "images", "buttercup_sticker.png")
        flower_path = os.path.join(project_root, "public", "images", "flower_sticker_1.png")
        
        if os.path.exists(blossom_path) and os.path.exists(bubbles_path) and os.path.exists(buttercup_path):
            try:
                char1 = Image.open(blossom_path).convert("RGBA") # Blossom
                char2 = Image.open(bubbles_path).convert("RGBA") # Bubbles
                char3 = Image.open(buttercup_path).convert("RGBA") # Buttercup
                
                # Make stickers larger (160x160) without enlarging the canvas/column itself
                char1.thumbnail((160, 160), Image.Resampling.LANCZOS)
                char2.thumbnail((160, 160), Image.Resampling.LANCZOS)
                char3.thumbnail((160, 160), Image.Resampling.LANCZOS)
                
                # Paste in corners:
                # Top Left: Blossom (shifted further to the right)
                canvas.paste(char1, (45, 100), char1)
                # Top Right: Bubbles
                canvas.paste(char2, (strip_w - 180, 100), char2)
                # Bottom Left: Ditch character duplicate and paste a cute flower sticker instead (3 cartoon characters total!)
                if os.path.exists(flower_path):
                    flower = Image.open(flower_path).convert("RGBA")
                    flower.thumbnail((100, 100), Image.Resampling.LANCZOS)
                    canvas.paste(flower, (35, strip_h - 240), flower)
                # Bottom Right: Buttercup
                canvas.paste(char3, (strip_w - 180, strip_h - 260), char3)
            except Exception as e:
                print(f"Error processing PPG stickers: {e}")

    elif style_theme == "polaroid_printer":
        draw.text((strip_w // 2, strip_h - 60), footer_text, fill=(255, 255, 255, 255), font=font_footer, anchor="mm")

    # Save output
    os.makedirs(os.path.dirname(out_path), exist_ok=True)
    canvas.save(out_path, "PNG")
    print(f"Success: Saved strip to {out_path}")
    return True

if __name__ == "__main__":
    if len(sys.argv) < 9:
        print("Usage: python generate_strip.py <img1> <img2> <img3> <output> <header_text> <footer_text> <style_theme> <photo_shape>")
        sys.exit(1)
    
    img1 = sys.argv[1]
    img2 = sys.argv[2]
    img3 = sys.argv[3]
    out = sys.argv[4]
    header = sys.argv[5]
    footer = sys.argv[6]
    theme = sys.argv[7]
    shape = sys.argv[8]
    
    generate_strip(img1, img2, img3, out, header, footer, theme, shape)
