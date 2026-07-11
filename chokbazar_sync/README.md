# Chokbazar Sync — Local Product Uploader

Sync products + images from your laptop to chokbazar.com in one command.

## Setup

```bash
cd chokbazar_sync
pip install -r requirements.txt
```

## Folder Structure

```
chokbazar_sync/
├── products.xlsx              # Excel spreadsheet
├── product_images/            # Product image folders
│   └── <image_folder>/        # Folder name matches 'image_folder' column
│       ├── main.jpg           # First image = primary
│       ├── side.jpg
│       └── box.jpg
├── sync_products.py           # This script
└── requirements.txt
```

## Excel Columns

| Column | Required | Description |
|--------|----------|-------------|
| name | ✅ | Product title (used for SEO title too if blank) |
| selling_price | ✅ | Retail price in BDT |
| stock | ✅ | Inventory count |
| image_folder | | Folder name in product_images/ |
| cost_price | | Wholesale/cost price |
| category | | Category name (auto-created if new) |
| sku | | Unique SKU |
| description | | Full description |
| short_description | | Short excerpt |
| specs | | Technical specs (appended to description) |
| tags | | Comma-separated tags |
| seo_title | | Meta title (defaults to product name) |
| seo_description | | Meta description (defaults to specs/name) |

## Usage

```bash
# Preview what will be uploaded
python sync_products.py --dry-run

# Upload everything
python sync_products.py --api-url https://chokbazar.com/api/products/import --token YOUR_TOKEN

# Skip products whose SKU already exists, regenerate sitemap
python sync_products.py --api-url ... --token ... --skip-existing --sitemap

# Use environment variables instead
export CHOKBAZAR_API_URL=https://chokbazar.com/api/products/import
export CHOKBAZAR_API_TOKEN=your_token_here
python sync_products.py
```

## Getting an API Token

1. Log in to chokbazar.com
2. Visit your profile/settings page
3. Create a new API token with appropriate permissions
4. Use that token with the --token flag
