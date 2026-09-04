import numpy as np
from PIL import Image, ImageFilter

def create_denim(width=600, height=1500):
    # Base colors for realistic indigo denim
    base = np.zeros((height, width, 3), dtype=np.uint8)
    
    for y in range(height):
        for x in range(width):
            # Create a 3/1 right-hand diagonal twill pattern
            val = (x + y * 2) % 4
            if val == 0:
                # Light blue/white warp thread
                base[y, x] = [140, 170, 210]
            elif val == 1:
                # Medium indigo
                base[y, x] = [50, 80, 130]
            else:
                # Dark indigo weft/base
                base[y, x] = [30, 55, 95]
                
    # Generate vertical loom variations (warp streaks)
    warp_variation = np.random.normal(0, 6, (1, width, 3))
    warp_variation = np.repeat(warp_variation, height, axis=0)
    
    # Add random cotton fiber noise
    fiber_noise = np.random.normal(0, 10, (height, width, 3))
    
    # Combine
    denim = base.astype(np.float32) + warp_variation + fiber_noise
    denim = np.clip(denim, 0, 255).astype(np.uint8)
    
    img = Image.fromarray(denim)
    # Light blur to blend threads realisticially
    img = img.filter(ImageFilter.GaussianBlur(0.4))
    return img

if __name__ == "__main__":
    img = create_denim()
    img.save("c:/xampp/htdocs/web_scrap/public/images/denim_bg_collage.png")
    print("Denim texture created successfully!")
