# Denis B plugin

Blocks and editor tools for the [Denis B theme](https://github.com/denischweya/denisb-theme), the WordPress version of [denis.swishfolio.com](https://denis.swishfolio.com/).

Version 2 is built on [Swishfolio Core](https://github.com/denischweya/swishfolio-core). Swishfolio Core provides most of the content blocks: features, portfolio, CV, testimonials, FAQ and links. It also provides the case study post type. This plugin adds only what Swishfolio Core lacks. Everything is edited in the block editor; there are no settings pages or meta boxes.

Requires Swishfolio Core (declared with `Requires Plugins`), WordPress 6.7+ and PHP 8.0+.

## Blocks

| Block | Used for |
| --- | --- |
| `denisb/mega-menu` | The Products link in the header. Add it inside a Navigation block. It opens a panel of items, with a note and an "All products" link in the footer. |
| `denisb/mega-menu-item` | One item in the mega menu: an icon image or letter, a label, a name, a description, a link and an optional badge. |
| `denisb/product-catalog` | The `/products/` page: heading, intro, type filters with counts, and a grid of product cards. |
| `denisb/product-card` | One theme or plugin in the catalogue: a screenshot, an icon or a letter tile. |
| `denisb/theme-toggle` | The system, light and dark buttons. The choice is saved in the visitor's browser. |
| `denisb/next-project` | The large "Next case study" link at the end of a case study. It follows the case studies' Order. |

## Editor tools

- **Rotating words.** Select text in a heading or paragraph, then choose **Rotating words** in the toolbar. Enter the words separated by commas. The theme animates them on the front end.
- **SEO panel.** Pages, posts and case studies get an SEO panel in the document sidebar. It holds a title, a description and JSON-LD. The plugin prints the description, robots, Open Graph and Twitter tags, and the JSON-LD. Add post types with the `denisb/seo_post_types` filter.

## Case studies

Case studies are Swishfolio Core projects (`sfcore_project`). This plugin changes how they behave:

- They live at `/case-study/<slug>/`, with no archive.
- They are labelled "Case studies".
- They can be ordered with the **Order** attribute, and the admin list sorts by it.
- Old `/?project=<slug>` links redirect to the matching case study.

The client name and live URL use Swishfolio Core's project fields. The excerpt holds the stack line shown in the work list.

## Swishfolio Core fixes

Swishfolio Core's dynamic blocks (portfolio, latest posts, form) drop the block's extra classes on the front end. The plugin adds them back, so block style variations work on those blocks too.

## Importing content

```sh
wp denisb import content.json --media-dir=path/to/media --user=admin
```

The file holds block markup. Running the import again updates posts with the same slug instead of duplicating them.

- `media` lists files by token. `{{media:<n>}}` becomes the file's URL. `9900<nnnn>` becomes the attachment ID, so block attributes like `"id":99000012` stay valid.
- `term_tokens` lists terms as `taxonomy:slug`; `9800<nnnn>` becomes that term's ID.
- `terms` creates terms. `posts` holds `type`, `slug`, `title`, `content`, `excerpt`, `order`, `parent`, `template`, `meta`, `terms` and `thumbnail`.
- `options` sets options. A `post:<type>:<path>` value becomes that post's ID, for example `"page_on_front": "post:page:home"`.

Template parts can be imported as `wp_template_part` posts with the `wp_theme` term.

## Development

```sh
npm install
npm run build   # or npm run start
```

Block sources are in `src/blocks`, and the editor tools are in `src/editor`. The compiled `build/` folder is committed, so the plugin works without a build step.

## License

GPL-2.0-or-later
