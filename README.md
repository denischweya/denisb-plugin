# Denis B plugin

Content types, fields and settings for the [Denis B theme](https://github.com/denischweya/denisb-theme), the WordPress version of [denis.swishfolio.com](https://denis.swishfolio.com/).

The plugin holds the content and the theme handles the display. Activate both.

## What it adds

| Post type | Under **Denis B** in the admin | Used for |
| --- | --- | --- |
| `db_project` | Case studies | The featured project, the spotlight, the client work list and the case study pages at `/case-study/<slug>/` |
| `db_product` | Products | The `/products/` catalogue, the Products mega menu, "Things I've shipped" on the homepage, and product landing pages at `/products/<slug>/` |
| `db_experience` | Experience | The experience timeline |
| `db_service` | Services | The services list |
| `db_step` | Process steps | The process steps |
| `db_testimonial` | Testimonials | Quotes |
| `db_faq` | FAQs | The FAQ list |

Each type has its own fields in a meta box under the editor. All fields are registered with `show_in_rest`.

- **Order.** Display order is the **Order** attribute (`menu_order`). The admin lists sort by it.
- **Product pages.** These use the block editor. Their sections are core blocks (groups, headings, paragraphs, buttons, images, lists, details) with the theme's class names, so they stay editable.
- **Other types.** These use the classic editor.

**Denis B → Site settings** holds the homepage copy:

- The hero: name, role, availability, rotating words, intro, links.
- The featured and spotlight projects.
- Section headings.
- The mega menu note.
- The contact band.
- SEO.

Old links in the form `/?project=<slug>` redirect to the matching case study.

## Importing content

```sh
wp denisb import content.json --media-dir=path/to/media --user=admin
```

The importer updates posts with the same slug, so you can run it again safely. Values can reference:

- `media:relative/path.png`: the file is uploaded once, and its attachment ID is stored.
- `{{media:relative/path.png}}` inside post content: replaced with the uploaded file's URL.
- `post:<post_type>:<slug>`: replaced with that post's ID. Settings use this to point at projects.

```json
{
  "posts": [
    { "type": "db_faq", "slug": "rates", "title": "What are your rates?", "content": "…", "order": 1 }
  ],
  "settings": { "featured_project": "post:db_project:brandora" }
}
```

## Development

- Requires WordPress 6.5+ and PHP 8.0+.
- Field definitions live in `includes/fields.php`. You can extend them with the `denisb/post_fields` filter.

## License

GPL-2.0-or-later
