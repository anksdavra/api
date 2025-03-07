# croissant-api

![Croissant API](https://github.com/shortlist-digital/croissant-api/workflows/Croissant%20API/badge.svg)

Custom APIs endpoints for croissant-heavy

The API documentation is generated using API Blueprint renderer called Aglio
To install Aglio run: `npm install -g aglio`.
To compile API documentation run: `aglio --theme-variables docs/colors.less -i docs/croissant-v1.apib -o api-docs/croissant-v1.html`

## Widgets expected return examples

### Advert

Available on: emails
<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "advert",
    "layout": "mpu",
    "assets": [
        {
            "link": "https://stylist.co.uk",
            "image": {}
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Amp Story Picker
Available on: training plans

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_picker",
    "title": "Week 1",
    "hide_widget_from_page": false,
    "amp_stories": [
        {
            "amp_story": {
                "id": 460432,
                "date": "2020-12-22T15:28:50",
                "date_gmt": "2020-12-22T15:28:50",
                "link": "https://staging.stylist.co.uk/fitness-health/beginners-week-1-day-1-december-2020/460432/amp-story",
                "title": {
                    "rendered": "Beginners Week 1 Day 1 December 2020"
                },
                "acf": {
                    "short_headline": "Beginner Week 1 Day 1 ",
                    "sell": "Get strong with the Beginner Week 1 Day 1 workout",
                    "hero_images": [],
                    "fullscreen_hero": "",
                    "brand_logo": "",
                    "category": {},
                    "series": [],
                    "package_ids": [],
                    "review_rating": ""
                },
                "_embedded": {},
                "sticky": false
            },
            "duration": "45 mins",
            "rendered_title": "Day 1"
        }
    ]
}
```

</details>

### Amp Story Tab

Labeled as `Amp Story Details` in the CMS.
<br>This widget has multiple possible return formats, depending on the chosen layout.
<br>Available on: amp stories

#### Text layout

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "text",
    "button_text": "click here",
    "button_link": "https://example.com",
    "text": "<p>Lorem ipsum</p>\n",
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

#### Media and text layout with image

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media_text",
    "button_text": "click here",
    "button_link": "https://example.com",
    "text": "<p>Lorem Ipsum</p>\n",
    "media": "image",
    "image": {},
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

#### Media and text layout with video

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media_text",
    "button_text": "click here",
    "button_link": "https://example.com",
    "text": "<p>Lorem Ipsum</p>\n",
    "media": "vimeo_video",
    "vimeo_video": "https://vimeo.com/491570711/7f16bdb076",
    "vimeo_link": "",
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

#### Media layout with image

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media",
    "button_text": "click here",
    "button_link": "https://example.com",
    "media": "image",
    "image": {},
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

#### Media layout with video

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media",
    "button_text": "click here",
    "button_link": "https://example.com",
    "media": "vimeo_video",
    "vimeo_video": "https://vimeo.com/491570711/7f16bdb076",
    "vimeo_link": "",
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

#### Media + media layout with 2 images

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media_media",
    "button_text": "click here",
    "button_link": "https://example.com",
    "media": "image",
    "image": {},
    "second_media": "image",
    "second_media_image": {},
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

#### Media + media layout with 2 videos

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media_media",
    "button_text": "click here",
    "button_link": "https://example.com",
    "media": "vimeo_video",
    "vimeo_video": "https://vimeo.com/491570711/7f16bdb076",
    "vimeo_link": "https://player.vimeo.com/external/491570711.sd.mp4?s=c932c4dde5399a2e970d6e4158febf49b3acad66&profile_id=139&oauth2_token_id=1429235805",
    "second_media": "vimeo_video",
    "second_media_vimeo_video": "https://vimeo.com/491570711/7f16bdb076",
    "second_media_vimeo_link": "https://player.vimeo.com/external/491570711.sd.mp4?s=c932c4dde5399a2e970d6e4158febf49b3acad66&profile_id=139&oauth2_token_id=1429235805",
    "backgound_colour": "#eeee22",
    "hide_widget_from_page": false
}
```

</details>

#### Media + media layout with image and video

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "amp_story_tab",
    "layout": "media_media",
    "button_text": "click here",
    "button_link": "https://example.com",
    "media": "image",
    "image": {},
    "second_media": "vimeo_video",
    "second_media_vimeo_video": "https://vimeo.com/491570711/7f16bdb076",
    "second_media_vimeo_link": "https://player.vimeo.com/external/491570711.sd.mp4?s=c932c4dde5399a2e970d6e4158febf49b3acad66&profile_id=139&oauth2_token_id=1429235805",
    "backgound_colour": "#81d742",
    "hide_widget_from_page": false
}
```

</details>

### Author Image

Available on: emails
<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "author_image",
    "image": {},
    "image_crop": "square",
    "hide_widget_from_page": false
}
```

</details>

### Button

Available on: posts, emails, longforms, sponsored longforms, sponsored posts, salespages
<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "button",
    "label": "click me",
    "url": "https://stylist.co.uk",
    "target": false,
    "hide_widget_from_page": false
}
```

</details>

### Classes picker

Available on: courses
<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "classes_picker",
    "title": "frfr",
    "widget_classes_picker_video_posts": [
        {
            "video_post": 446018,
            "duration": "3 hours 25 minutes",
            "rendered_title": "Biohack your way back from burnout"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Content group

Available on: sponsored longforms
<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "content-group",
    "id": "tuscany",
    "type": "end",
    "style": "tabs",
    "hide_widget_from_page": false
}
```

</details>

### Countdown

Available on: sales pages
<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "countdown",
    "countdown_header": "Countdown header here",
    "countdown_to_date": "2021-05-21T00:00:00",
    "button_text": "BUY TICKETS",
    "button_link": "http://stylist.co.uk",
    "background_colour": "#1e73be",
    "hide_widget_from_page": false
}
```

</details>

### Divider

Available on: posts, emails, longforms, sponsored longforms, sponsored posts, sales pages
<details>
<summary>Expected return</summary>
<br>

```json
{
"acf_fc_layout": "divider",
"hide_widget_from_page": false
}
```

</details>

### Email product collection

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "email-product-collection",
    "header_type": "text",
    "header_image": {},
    "layout_header": "Text header",
    "layout_sell": "franchise sell",
    "author_image": {},
    "project_id": "123456",
    "products": [
        {
            "thumbnail": {},
            "product_brand": "",
            "currency": "gbp",
            "price": "",
            "product_text": "",
            "product_description": "",
            "button_text": "",
            "button_url": "",
            "sponsored": false,
            "sponsored_link": ""
        }
    ],
    "sponsored_widget": false,
    "sponsored_widget_label": "",
    "sponsored_widget_link": "",
    "hide_widget_from_page": false
}
```

</details>

### Embed

Available on: posts, longforms, sponsored longforms, sponsored posts, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
"acf_fc_layout": "embed",
"embed": "<div class=\"iframely-youtube iframely-player iframely-embed\"><div class=\"iframely-responsive\"><iframe title=\"70S SOUL -  Aretha Franklin, Marvin Gaye, AL Green, Sam Cooke, The Temptations and more\" data-iframely-url=\"https://embeds.stylist.co.uk/Ijtkh3e?maxheight=1200\" data-img allowfullscreen scrolling=\"no\" allow=\"accelerometer *; clipboard-write *; encrypted-media *; gyroscope *; picture-in-picture *;\"></iframe></div></div>",
"embed_link": "https://www.youtube.com/watch?v=TqVeG4oI444",
"width": "large",
"autoplay": false,
"caption": "<p>some caption</p>\n",
"mobile_embed": "<div class=\"iframely-youtube iframely-player iframely-embed\"><div class=\"iframely-responsive\"><iframe title=\"70S SOUL -  Aretha Franklin, Marvin Gaye, AL Green, Sam Cooke, The Temptations and more\" data-iframely-url=\"https://embeds.stylist.co.uk/Ijtkh3e?maxheight=1200\" data-img allowfullscreen scrolling=\"no\" allow=\"accelerometer *; clipboard-write *; encrypted-media *; gyroscope *; picture-in-picture *;\"></iframe></div></div>",
"mobile_embed_link": "https://www.youtube.com/watch?v=TqVeG4oI444",
"hide_widget_from_page": false
}
```

</details>

### Faqs generator

Labeled as `FAQs` in the CMS
<br>Available on: sales pages

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "faqs_generator",
    "faqs": [
        {
            "question": "What’s included with my ticket?",
            "answer": "<p>blahhh blahhh</p>\n"
        },
        {
            "question": "How do I access Stylist Live @ Home?",
            "answer": "<p>Stylist Live @ Home is ticketed and the link to access it will be available from 08:00 on Saturday 14 November with the first session starting at 09:00. All content will be available for you to watch online until 00:59 on Sunday 29 November. You will need to be logged in to your MyStylist account to access the event platform and watch the live sessions.Stylist Live @ Home is ticketed and the link to access it will be available from 08:00 on Saturday 14 November with the first session starting at 09:00. All content will be available for you to watch online until 00:59 on Sunday 29 November. You will need to be logged in to your MyStylist account to access the event platform and watch the live sessions.\n\t  </p>\n"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Feature list picker

<br>Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "feature_list_picker",
    "title": "woaoaoa",
    "layout": "left",
    "crop": "portrait",
    "list": [
        {
        "title": "Mood changer",
        "image": {},
        "paragraph": "<p>It’s not exactly new, but I’ve just been introduced to the world of <a href=\"https://www.instagram.com/maylindstromskin/\" target=\"_blank\" rel=\"noopener noreferrer\">May Lindstrom</a> and am intoxicated. The Jasmine Garden Botanical Face Mist (<a href=\"https://www.spacenk.com/uk/en_GB/skincare/toner/mists-sprays/the-jasmine-garden-botanical-facial-mist-MUK200022477.html\" target=\"_blank\" rel=\"noopener noreferrer\">£62, Space NK</a>) combines colloidal silver (antibacterial and heals skin) and witch hazel with the most uplifting blend of rose and ylang ylang. It works exceptionally before masks and serums to increase absorption, and I use it as part of my morning routine. Trust me, it makes such a difference. <b><a href=\"https://www.instagram.com/avawelsingk/\" target=\"_blank\" rel=\"noopener noreferrer\">Ava Welsing-Kitcher</a>, junior beauty writer<br />\n\t  </b></p>\n",
        "button_text": "",
        "button_link": ""
        },
        {
        "title": "Peachy keen",
        "image": {},
        "paragraph": "<p>Call me basic, but there’s something insanely satisfying about plonking a fancy bottle of hand wash and lotion next to your sink. Right now, I’m using & Other Stories’ new Avant-Garde Air collection (<a href=\"https://www.stories.com/en_gbp/beauty/whats-new-beauty/product.hand-soap-avant-garde-air.0159486053.html\" target=\"_blank\" rel=\"noopener noreferrer\">£6 each</a>). It’s the colour of orange squash, so already pretty joy-inducing, but it also smells heavenly – like peaches sprinkled with brown sugar and drenched in vanilla cream. Dreamy. <b><a href=\"https://www.instagram.com/shannonrpeter/?hl=en\" target=\"_blank\" rel=\"noopener noreferrer\">Shannon Peter</a>, beauty director<br />\n\t  </b></p>\n",
        "button_text": "",
        "button_link": ""
        }
    ],
    "hide_widget_from_page": false,
    "author": {
        "id": 595,
        "name": "Naomi Joseph",
        "link": "https://staging.stylist.co.uk/author/naomijoseph",
        "slug": "naomijoseph"
    },
    "standfirst": "From inexpensive treats to investment haircare. Plus! We’ve found a genuinely good spot treatment and a pop-up offering skin consultations for just £5&nbsp; &nbsp; &nbsp; &nbsp;"
}
```

</details>

### Html

Available on: posts, pages, emails, longforms, sponsored longforms, sponsored posts, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "html",
    "html": "<p>This is paragraph</p>",
    "width": "medium",
    "hide_widget_from_page": false
}
```

</details>

### Heading

Available on: posts, pages, emails, longforms, sponsored longforms, sponsored posts, sales pages

<details>
<summary>Expected return</summary>
<br>

```json
 {
    "acf_fc_layout": "heading",
    "text": "Heeeaaaaadiiiing",
    "alignment": "left",
    "hide_widget_from_page": false
}
```

</details>

### Image

Available on: posts, pages, longforms, quiz posts, sponsored quiz posts, sponsored longforms, sponsored posts, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "image",
    "image": {},
    "crop": "letterbox",
    "mobile_image": {},
    "width": "medium",
    "link": "https://stylist.co.uk",
    "open_link_in_new_tab": true,
    "position": "center",
    "hide_widget_from_page": false
}
```

</details>

### Email Image

Labeled as `Image` in the CMS
<br>Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "email_image",
    "image": {},
    "link": "https://stylist.co.uk",
    "hide_widget_from_page": false
}
```

</details>

### Images repeater

Available on: sales pages

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "images-repeater",
    "images": [
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/04/05161922/552_cover_shalom_digital_v1_72ppi.jpg",
            "alt": "magazine cover",
            "width": 1030,
            "height": 1375,
            "caption": "magazine cover",
            "description": "stylist",
            "sizes": {},
            "webp_sizes": []
        }
    ],
    "hide_widget_from_page": false,
    "images_links": [
        "http://stylist.co.uk",
        "http://stylist.co.uk",
        "http://stylist.co.uk"
    ]
}
```

</details>

### Interactive image

This widget has multiple possible return formats, depending on the chosen layout.
<br>Available on: longforms, sponsored longforms

#### Image slider and diptych layouts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "interactive_image",
    "layout_interaction": "slider",
    "parallax_notes": [],
    "grid_notes": [],
    "image_collection": false,
    "first_image": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
        "alt": "hello",
        "width": 1920,
        "height": 1133,
        "caption": "asdas",
        "description": "asdasd",
        "sizes": {},
        "webp_sizes": []
    },
    "second_image": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
        "alt": "hello",
        "width": 1920,
        "height": 1133,
        "caption": "asdas",
        "description": "asdasd",
        "sizes": {},
        "webp_sizes": []
    },
    "third_image": false,
    "crop": "original",
    "hide_widget_from_page": false
}
```

</details>

#### Parallax layout

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "interactive_image",
    "layout_interaction": "parallax",
    "parallax_notes": [],
    "grid_notes": [],
    "image_collection": [
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
            "alt": "hello",
            "width": 1920,
            "height": 1133,
            "caption": "asdas",
            "description": "asdasd",
            "sizes": {},
            "webp_sizes": []
        }
    ],
    "third_image": false,
    "crop": "original",
    "hide_widget_from_page": false,
    "mobile_image_collection": [
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
            "alt": "hello",
            "width": 1920,
            "height": 1133,
            "caption": "asdas",
            "description": "asdasd",
            "sizes": {},
            "webp_sizes": []
        },
    ]
}
```

</details>

#### Triptych layout

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "interactive_image",
    "layout_interaction": "trippy",
    "parallax_notes": [],
    "grid_notes": [],
    "image_collection": false,
    "first_image": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
        "alt": "hello",
        "width": 1920,
        "height": 1133,
        "caption": "asdas",
        "description": "asdasd",
        "sizes": {},
        "webp_sizes": []
    },
    "second_image": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
        "alt": "hello",
        "width": 1920,
        "height": 1133,
        "caption": "asdas",
        "description": "asdasd",
        "sizes": {},
        "webp_sizes": []
    },
    "third_image": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
        "alt": "hello",
        "width": 1920,
        "height": 1133,
        "caption": "asdas",
        "description": "asdasd",
        "sizes": {},
        "webp_sizes": []
    },
    "crop": "original",
    "hide_widget_from_page": false
}
```

</details>

#### Grid layout

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "interactive_image",
    "layout_interaction": "grid",
    "parallax_notes": [],
    "grid_notes": [],
    "image_collection": [
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
            "alt": "hello",
            "width": 1920,
            "height": 1133,
            "caption": "asdas",
            "description": "asdasd",
            "sizes": {},
            "webp_sizes": []
        },
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
            "alt": "hello",
            "width": 1920,
            "height": 1133,
            "caption": "asdas",
            "description": "asdasd",
            "sizes": {},
            "webp_sizes": []
        },
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
            "alt": "hello",
            "width": 1920,
            "height": 1133,
            "caption": "asdas",
            "description": "asdasd",
            "sizes": {},
            "webp_sizes": []
        },
        {
            "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/07/19095637/swtc_wednesdays_dottie_vimeo_2107_v2.jpg",
            "alt": "hello",
            "width": 1920,
            "height": 1133,
            "caption": "asdas",
            "description": "asdasd",
            "sizes": {},
            "webp_sizes": []
        }
    ],
    "first_image": false,
    "second_image": false,
    "third_image": false,
    "crop": "original",
    "hide_widget_from_page": false
}
```

</details>

### Link collection

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "link_collection",
    "title": "Today's Talk",
    "expand_posts": true,
    "read_more": true,
    "posts": [
        {
            "headline": "'Feminist' Ryan Gosling is internet hit",
            "link": "https://staging.stylist.co.uk/people/feminist-ryan-gosling-is-internet-hit/13491",
            "image": {},
            "sponsor_link": null,
            "sponsor_name": null,
            "sponsor_label": null
        }
    ],
    "external_links": [
        {
            "headline": "External link",
            "link": "https://stylist.co.uk",
            "source": "source"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Listicle

This widget has multiple possible return formats, depending on the chosen media type.
<br>Available on: posts, longforms, sponsored longforms, sponsored posts, sales pages

#### Image media type

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "listicle",
    "item": [
        {
            "title": "Listicle Inferno",
            "media_type": "image",
            "image": {},
            "image_crop": "original",
            "mobile_image": {},
            "embed": null,
            "embed_link": "",
            "mobile_embed": null,
            "mobile_embed_link": "",
            "autoplay": false,
            "video": false,
            "placeholder": false,
            "mobile_video": false,
            "mobile_placeholder": false,
            "width": "full",
            "paragraph": "<p></p>\n",
            "label": "",
            "url": ""
        }
    ],
    "numbered": false,
    "descending": false,
    "horizontal_split": false,
    "hide_widget_from_page": false
}
```

</details>

#### Embed media type

<details>
<summary>Expected return</summary>
<br>

```json
{
    "title": "Listicle with embed",
    "media_type": "embed",
    "image": false,
    "image_crop": "original",
    "mobile_image": false,
    "embed": "<div class=\"iframely-youtube iframely-player iframely-embed\"><div class=\"iframely-responsive\"><iframe title=\"Lana Del Rey - Blue Jeans\" data-iframely-url=\"https://embeds.stylist.co.uk/i6bcDjU?maxheight=1200\" data-img allowfullscreen scrolling=\"no\" allow=\"accelerometer *; clipboard-write *; encrypted-media *; gyroscope *; picture-in-picture *;\"></iframe></div></div>",
    "embed_link": "https://www.youtube.com/watch?v=JRWox-i6aAk&list=RDMM&index=11",
    "mobile_embed": "",
    "mobile_embed_link": "",
    "autoplay": false,
    "video": false,
    "placeholder": false,
    "mobile_video": false,
    "mobile_placeholder": false,
    "width": "full",
    "paragraph": "",
    "label": "",
    "url": ""
}
```

</details>

#### Looping video media type

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "listicle",
    "item": [
        {
            "title": "Listicle with looping video",
            "media_type": "loop",
            "image": false,
            "image_crop": "original",
            "mobile_image": false,
            "embed": null,
            "embed_link": "",
            "mobile_embed": null,
            "mobile_embed_link": "",
            "autoplay": false,
            "video": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/03/15150359/stylist_sports_direct_cricket.mp4",
            "placeholder": {},
            "mobile_video": false,
            "mobile_placeholder": "",
            "width": "medium",
            "paragraph": "",
            "label": "",
            "url": ""
        }
    ],
    "numbered": false,
    "descending": false,
    "horizontal_split": false,
    "hide_widget_from_page": false
}
```

</details>

### Looping video

Available on: posts, longforms, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "looping_video",
    "video": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/03/15150359/stylist_sports_direct_cricket.mp4",
    "placeholder": {},
    "mobile_video": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/03/15150359/stylist_sports_direct_cricket.mp4",
    "mobile_placeholder": {},
    "width": "medium",
    "hide_widget_from_page": false
}
```

</details>

### Newsletter signup
Available on: posts, longforms, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "newsletter_signup",
    "hide_widget_from_page": false,
    "name": "COMPETITIONS",
    "parent": null,
    "slug": "competitions",
    "telemetry_vertical": 27,
    "telemetry_vertical_id": 27,
    "sell": "Want to be the first to hear about our exclusive reader competitions, offers and discounts? Sign up for the <em>Competitions + Offers</em> email",
    "background_colour": "#ffd1b3",
    "text_colour": "#000000",
    "button_colour": "#ff6600",
    "button_text_colour": "#000000",
    "primary_colour": "#ffd1b3",
    "accent_colour": "#000000"
}
```

</details>

### One click subscription

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "one_click_subscription",
    "title": "One Click Subscription Widget",
    "layout_heading": "One click sub",
    "layout_description": "",
    "verticals": [
        {
            "name": "Partnership",
            "slug": "partnership",
            "sell": "",
            "vertical_id": 0,
            "thumbnail_image": {},
            "button_text": "Sign Up Now"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Paragraph

Available on: posts, pages, emails, longforms, sponsored longforms, sponsored posts, sales pages, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "paragraph",
    "paragraph": "<p>This is a fantastic paragraph</p>\n",
    "hide_widget_from_page": false
}
```

</details>

### Email post collection

Labeled as `Post Collection` in the CMS
<br>Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "email_post_collection",
    "layout": "center",
    "title": "Email post collection",
    "sell": "some sell here",
    "posts": [
        {
            "post": {
                "id": 48022,
                "date": "2011-09-26T10:15:00",
                "date_gmt": "2011-09-26T10:15:00",
                "link": "https://staging.stylist.co.uk/life/corpse-found-alive-in-brazilian-morgue/48022",
                "title": {},
                "acf": {
                    "short_headline": "'Corpse' found alive in Brazilian morgue",
                    "sell": "Female victim spent two hours in body bag",
                    "hero_images": [],
                    "fullscreen_hero": "",
                    "brand_logo": "",
                    "category": {},
                    "series": [],
                    "package_ids": [],
                    "review_rating": ""
                },
                "_embedded": {},
                "sticky": false
            },
            "button_text": "click here",
            "image": {}
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Product carousel

Labeled as `Product Collection` in the CMS
<br>Available on: posts, longforms, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "product-carousel",
    "Layout": "grid",
    "crop": "original",
    "products": [
        {
            "thumbnail": {},
            "product_brand": "",
            "currency": "gbp",
            "price": "",
            "product_text": "",
            "product_description": "",
            "button_text": "",
            "button_url": "",
            "sponsored": false,
            "sponsored_link": ""
        }
    ],
"sponsored_widget": false,
"sponsored_widget_label": "",
"sponsored_widget_link": "",
"hide_widget_from_page": false
}
```

</details>

### Pull quote

<br>Available on: posts, longforms, sponsored longforms, sponsored posts, sales pages

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "pull-quote",
    "text": "Some People Are Worth Melting For",
    "quote_author": "Olaf",
    "quote_bg_colour": "#81d742",
    "width": "medium",
    "position": "center",
    "hide_widget_from_page": false
}
```

</details>

### Quick links

<br>Available on: sponsored longforms

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "quick-links",
    "quick_links": [
        {
            "acf_fc_layout": "link",
            "label": "Tuscany",
            "id": "tuscany"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Related articles

Available on: posts, longforms, sponsored longforms, sponsored posts, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "related_articles",
    "title": "You may also like",
    "posts": [
        {
            "id": 47523,
            "date": "2011-07-21T23:00:00",
            "date_gmt": "2011-07-21T23:00:00",
            "link": "https://staging.stylist.co.uk/life/beer-for-women-set-for-release/47523",
            "title": {},
            "acf": {
                "short_headline": "'Beer for women' set for release",
                "sell": "Brewing company targets female drinkers",
                "hero_images": [],
                "fullscreen_hero": "",
                "brand_logo": "",
                "category": {},
                "series": [],
                "package_ids": [],
                "review_rating": ""
            },
            "_embedded": {},
            "sticky": false
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Snippet picker

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "snippet_picker",
    "layout_header": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2019/05/09173138/goingout_franchise_190516_v1.png"
    },
    "layout_sell": "franchise sell",
    "layout": "mixed",
    "crop": "original",
    "collection": [
        {
            "headline": "Whisk Dessert Bar ",
            "sell": "Angel Central Shopping Centre, 23 Parkfield St, N1",
            "paragraph": "<p>When it comes to eating out, the world can be divided into two groups: those who always choose a starter over dessert, and those who consider a meal wasted if it doesn’t end with pudding. If you’re in the latter camp, visit Angel between now and 3 August for the Whisk pop-up, where you can feast on a three-course dessert tasting menu paired with tea or wine: think extravagant sorbets, mousses, meringues and more. <i><a href=\"http://www.whiskdessertbar.co.uk/\" target=\"_blank\" rel=\"noopener noreferrer\">Angel Central Shopping Centre, 23 Parkfield St, N1</a> </i><b>● MC<br />\n\t  </b></p>\n",
            "button_text": "",
            "button_link": "",
            "sponsor": false,
            "image": {},
            "project_id": "123456"
        },
        {
            "headline": "Ganni x Levi’s is back – and this time, you can buy everything ",
            "sell": "",
            "paragraph": "<p>It seems like only yesterday that Ganni x Levi’s breezed into our lives with its innovative <a href=\"https://repeat.ganni.com/gb/en/ganni-x-levis/\" target=\"_blank\" rel=\"noopener noreferrer\">rental-only upcycled denim collection</a>. Fusing the Danish brand’s Scandi-cool style with the denim label’s all-American heritage, we coveted every single piece when it launched last summer – not least because they were all designed with a commitment to eco-friendly practices. </p>\n<p>Now, <a href=\"https://www.ganni.com/en-gb/levis-x-ganni.html\" target=\"_blank\" rel=\"noopener noreferrer\">Ganni x Levi’s is back</a> with a second instalment of sustainable denim to call your very own. Launching today, the new collection has been created with cottonised hemp, which has been grown with less water and fewer pesticides than traditional cotton (and, rather impressively, looks and feels just like denim). The 14-piece edit has the same feminine yet functional feel as its predecessor, including <a href=\"https://www.ganni.com/en-gb/medium-indigo-denim-shirt-F6088.html?dwvar_F6088_color=Medium%20Indigo\" target=\"_blank\" rel=\"noopener noreferrer\">denim tops with micro-frilled collars</a>, <a href=\"https://www.ganni.com/en-gb/dark-indigo-denim-dress-F6086.html?dwvar_F6086_color=Dark%20Indigo\" target=\"_blank\" rel=\"noopener noreferrer\">dark panelled dresses</a> and a <a href=\"https://www.ganni.com/en-gb/dark-indigo-denim-blazer-F6085.html?dwvar_F6085_color=Dark%20Indigo\" target=\"_blank\" rel=\"noopener noreferrer\">double-breasted jacket</a> that looks perfect for the Big Spring Coat Change. This being the pandemic era, there are also <a href=\"https://www.ganni.com/en-gb/tops/\" target=\"_blank\" rel=\"noopener noreferrer\">slogan T-shirts and soft sweatshirts</a> that are ideal for downtime. Because we’re not letting loungewear go without a fight. <b>From £83, available at <a href=\"https://www.ganni.com/en-gb/levis-x-ganni.html\" target=\"_blank\" rel=\"noopener noreferrer\">ganni.com</a> and <a href=\"https://www.levi.com/GB/en_GB/levis-x-ganni/c/levi_women_collections_ganni\" target=\"_blank\" rel=\"noopener noreferrer\">levi.com</a></b></p>\n",
            "button_text": "",
            "button_link": "",
            "sponsor": false,
            "image": {},
            "project_id": "123456"
        }
    ],
    "hide_widget_from_page": false,
    "authors": [
        {
            "id": 405,
            "name": "Kat Poole",
            "link": "https://staging.stylist.co.uk/author/katpoole",
            "slug": "katpoole"
        },
        {
            "id": 158,
            "name": "Christobel Hastings",
            "link": "https://staging.stylist.co.uk/author/christobelhastings",
            "slug": "christobelhastings"
        }
    ],
    "first_image": false,
    "second_image": false,
    "third_image": false,
    "crop": "original",
    "hide_widget_from_page": false
}
```

</details>

### Link collection

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "link_collection",
    "title": "Today's Talk",
    "expand_posts": true,
    "read_more": true,
    "posts": [
        {
            "headline": "'Feminist' Ryan Gosling is internet hit",
            "link": "https://staging.stylist.co.uk/people/feminist-ryan-gosling-is-internet-hit/13491",
            "image": {},
            "sponsor_link": null,
            "sponsor_name": null,
            "sponsor_label": null
        }
    ],
    "external_links": [
        {
            "headline": "External link",
            "link": "https://stylist.co.uk",
            "source": "source"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Listicle

This widget has multiple possible return formats, depending on the chosen media type.
<br>Available on: posts, longforms, sponsored longforms, sponsored posts, sales pages

#### Image media type

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "listicle",
    "item": [
        {
            "title": "Listicle Inferno",
            "media_type": "image",
            "image": {},
            "image_crop": "original",
            "mobile_image": {},
            "embed": null,
            "embed_link": "",
            "mobile_embed": null,
            "mobile_embed_link": "",
            "autoplay": false,
            "video": false,
            "placeholder": false,
            "mobile_video": false,
            "mobile_placeholder": false,
            "width": "full",
            "paragraph": "<p></p>\n",
            "label": "",
            "url": ""
        }
    ],
    "numbered": false,
    "descending": false,
    "horizontal_split": false,
    "hide_widget_from_page": false
}
```

</details>

#### Embed media type

<details>
<summary>Expected return</summary>
<br>

```json
{
    "title": "Listicle with embed",
    "media_type": "embed",
    "image": false,
    "image_crop": "original",
    "mobile_image": false,
    "embed": "<div class=\"iframely-youtube iframely-player iframely-embed\"><div class=\"iframely-responsive\"><iframe title=\"Lana Del Rey - Blue Jeans\" data-iframely-url=\"https://embeds.stylist.co.uk/i6bcDjU?maxheight=1200\" data-img allowfullscreen scrolling=\"no\" allow=\"accelerometer *; clipboard-write *; encrypted-media *; gyroscope *; picture-in-picture *;\"></iframe></div></div>",
    "embed_link": "https://www.youtube.com/watch?v=JRWox-i6aAk&list=RDMM&index=11",
    "mobile_embed": "",
    "mobile_embed_link": "",
    "autoplay": false,
    "video": false,
    "placeholder": false,
    "mobile_video": false,
    "mobile_placeholder": false,
    "width": "full",
    "paragraph": "",
    "label": "",
    "url": ""
}
```

</details>

#### Looping video media type

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "listicle",
    "item": [
        {
            "title": "Listicle with looping video",
            "media_type": "loop",
            "image": false,
            "image_crop": "original",
            "mobile_image": false,
            "embed": null,
            "embed_link": "",
            "mobile_embed": null,
            "mobile_embed_link": "",
            "autoplay": false,
            "video": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/03/15150359/stylist_sports_direct_cricket.mp4",
            "placeholder": {},
            "mobile_video": false,
            "mobile_placeholder": "",
            "width": "medium",
            "paragraph": "",
            "label": "",
            "url": ""
        }
    ],
    "numbered": false,
    "descending": false,
    "horizontal_split": false,
    "hide_widget_from_page": false
}
```

</details>

### Looping video

Available on: posts, longforms, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "looping_video",
    "video": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/03/15150359/stylist_sports_direct_cricket.mp4",
    "placeholder": {},
    "mobile_video": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2021/03/15150359/stylist_sports_direct_cricket.mp4",
    "mobile_placeholder": {},
    "width": "medium",
    "hide_widget_from_page": false
}
```

</details>

### Newsletter signup

Available on: posts, longforms, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "newsletter_signup",
    "hide_widget_from_page": false,
    "name": "COMPETITIONS",
    "parent": null,
    "slug": "competitions",
    "telemetry_vertical": 27,
    "telemetry_vertical_id": 27,
    "sell": "Want to be the first to hear about our exclusive reader competitions, offers and discounts? Sign up for the <em>Competitions + Offers</em> email",
    "background_colour": "#ffd1b3",
    "text_colour": "#000000",
    "button_colour": "#ff6600",
    "button_text_colour": "#000000",
    "primary_colour": "#ffd1b3",
    "accent_colour": "#000000"
}
```

</details>

### One click subscription

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "one_click_subscription",
    "title": "One Click Subscription Widget",
    "layout_heading": "One click sub",
    "layout_description": "",
    "verticals": [
        {
            "name": "Partnership",
            "slug": "partnership",
            "sell": "",
            "vertical_id": 0,
            "thumbnail_image": {},
            "button_text": "Sign Up Now"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Paragraph

Available on: posts, pages, emails, longforms, sponsored longforms, sponsored posts, sales pages, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "paragraph",
    "paragraph": "<p>This is a fantastic paragraph</p>\n",
    "hide_widget_from_page": false
}
```

</details>

### Email post collection

Labeled as `Post Collection` in the CMS
<br>Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "email_post_collection",
    "layout": "center",
    "title": "Email post collection",
    "sell": "some sell here",
    "posts": [
        {
            "post": {
                "id": 48022,
                "date": "2011-09-26T10:15:00",
                "date_gmt": "2011-09-26T10:15:00",
                "link": "https://staging.stylist.co.uk/life/corpse-found-alive-in-brazilian-morgue/48022",
                "title": {},
                "acf": {
                    "short_headline": "'Corpse' found alive in Brazilian morgue",
                    "sell": "Female victim spent two hours in body bag",
                    "hero_images": [],
                    "fullscreen_hero": "",
                    "brand_logo": "",
                    "category": {},
                    "series": [],
                    "package_ids": [],
                    "review_rating": ""
                },
                "_embedded": {},
                "sticky": false
            },
            "button_text": "click here",
            "image": {}
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Product carousel

Labeled as `Product Collection` in the CMS
<br>Available on: posts, longforms, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "product-carousel",
    "Layout": "grid",
    "crop": "original",
    "products": [
        {
            "thumbnail": {},
            "product_brand": "",
            "currency": "gbp",
            "price": "",
            "product_text": "",
            "product_description": "",
            "button_text": "",
            "button_url": "",
            "sponsored": false,
            "sponsored_link": ""
        }
    ],
    "sponsored_widget": false,
    "sponsored_widget_label": "",
    "sponsored_widget_link": "",
"hide_widget_from_page": false
}
```

</details>

### Pull quote

<br>Available on: posts, longforms, sponsored longforms, sponsored posts, sales pages

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "pull-quote",
    "text": "Some People Are Worth Melting For",
    "quote_author": "Olaf",
    "quote_bg_colour": "#81d742",
    "width": "medium",
    "position": "center",
    "hide_widget_from_page": false
}
```

</details>

### Quick links

<br>Available on: sponsored longforms

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "quick-links",
    "quick_links": [
        {
            "acf_fc_layout": "link",
            "label": "Tuscany",
            "id": "tuscany"
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Related articles

Available on: posts, longforms, sponsored longforms, sponsored posts, video posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "related_articles",
    "title": "You may also like",
    "posts": [
        {
            "id": 47523,
            "date": "2011-07-21T23:00:00",
            "date_gmt": "2011-07-21T23:00:00",
            "link": "https://staging.stylist.co.uk/life/beer-for-women-set-for-release/47523",
            "title": {},
            "acf": {
                "short_headline": "'Beer for women' set for release",
                "sell": "Brewing company targets female drinkers",
                "hero_images": [],
                "fullscreen_hero": "",
                "brand_logo": "",
                "category": {},
                "series": [],
                "package_ids": [],
                "review_rating": ""
            },
            "_embedded": {},
            "sticky": false
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

### Snippet picker

Available on: emails

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "snippet_picker",
    "layout_header": {
        "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2019/05/09173138/goingout_franchise_190516_v1.png"
    },
    "layout_sell": "franchise sell",
    "layout": "mixed",
    "crop": "original",
    "collection": [
        {
            "headline": "Whisk Dessert Bar ",
            "sell": "Angel Central Shopping Centre, 23 Parkfield St, N1",
            "paragraph": "<p>When it comes to eating out, the world can be divided into two groups: those who always choose a starter over dessert, and those who consider a meal wasted if it doesn’t end with pudding. If you’re in the latter camp, visit Angel between now and 3 August for the Whisk pop-up, where you can feast on a three-course dessert tasting menu paired with tea or wine: think extravagant sorbets, mousses, meringues and more. <i><a href=\"http://www.whiskdessertbar.co.uk/\" target=\"_blank\" rel=\"noopener noreferrer\">Angel Central Shopping Centre, 23 Parkfield St, N1</a> </i><b>● MC<br />\n\t  </b></p>\n",
            "button_text": "",
            "button_link": "",
            "sponsor": false,
            "image": {}
        },
        {
            "headline": "Ganni x Levi’s is back – and this time, you can buy everything ",
            "sell": "",
            "paragraph": "<p>It seems like only yesterday that Ganni x Levi’s breezed into our lives with its innovative <a href=\"https://repeat.ganni.com/gb/en/ganni-x-levis/\" target=\"_blank\" rel=\"noopener noreferrer\">rental-only upcycled denim collection</a>. Fusing the Danish brand’s Scandi-cool style with the denim label’s all-American heritage, we coveted every single piece when it launched last summer – not least because they were all designed with a commitment to eco-friendly practices. </p>\n<p>Now, <a href=\"https://www.ganni.com/en-gb/levis-x-ganni.html\" target=\"_blank\" rel=\"noopener noreferrer\">Ganni x Levi’s is back</a> with a second instalment of sustainable denim to call your very own. Launching today, the new collection has been created with cottonised hemp, which has been grown with less water and fewer pesticides than traditional cotton (and, rather impressively, looks and feels just like denim). The 14-piece edit has the same feminine yet functional feel as its predecessor, including <a href=\"https://www.ganni.com/en-gb/medium-indigo-denim-shirt-F6088.html?dwvar_F6088_color=Medium%20Indigo\" target=\"_blank\" rel=\"noopener noreferrer\">denim tops with micro-frilled collars</a>, <a href=\"https://www.ganni.com/en-gb/dark-indigo-denim-dress-F6086.html?dwvar_F6086_color=Dark%20Indigo\" target=\"_blank\" rel=\"noopener noreferrer\">dark panelled dresses</a> and a <a href=\"https://www.ganni.com/en-gb/dark-indigo-denim-blazer-F6085.html?dwvar_F6085_color=Dark%20Indigo\" target=\"_blank\" rel=\"noopener noreferrer\">double-breasted jacket</a> that looks perfect for the Big Spring Coat Change. This being the pandemic era, there are also <a href=\"https://www.ganni.com/en-gb/tops/\" target=\"_blank\" rel=\"noopener noreferrer\">slogan T-shirts and soft sweatshirts</a> that are ideal for downtime. Because we’re not letting loungewear go without a fight. <b>From £83, available at <a href=\"https://www.ganni.com/en-gb/levis-x-ganni.html\" target=\"_blank\" rel=\"noopener noreferrer\">ganni.com</a> and <a href=\"https://www.levi.com/GB/en_GB/levis-x-ganni/c/levi_women_collections_ganni\" target=\"_blank\" rel=\"noopener noreferrer\">levi.com</a></b></p>\n",
            "button_text": "",
            "button_link": "",
            "sponsor": false,
            "image": {}
        }
    ],
    "hide_widget_from_page": false,
    "authors": [
        {
            "id": 405,
            "name": "Kat Poole",
            "link": "https://staging.stylist.co.uk/author/katpoole",
            "slug": "katpoole"
        },
        {
            "id": 158,
            "name": "Christobel Hastings",
            "link": "https://staging.stylist.co.uk/author/christobelhastings",
            "slug": "christobelhastings"
        }
    ]
}
```

</details>

### Telemetry acquisition

Available on: posts, sponsored longforms, sponsored posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "telemetry_acquisition",
    "data_to_capture": [
        "fullName",
        "address"
    ],
    "telemetry_id": "2810",
    "promotion_telemetry_id": "743",
    "start_time": "2021-07-19 00:00:00",
    "end_time": "2021-07-25 00:00:00",
    "additional_features": [
        "optins"
    ],
    "competition_question": "`Yes?<br>",
    "competition_telemetry_id": "430",
    "competition_answers": [
        {
            "answer_text": "Yes",
            "answer_correct": true,
            "telemetry_id": "1185"
        },
        {
            "answer_text": "No",
            "answer_correct": false,
            "telemetry_id": "1186"
        }
    ],
    "optins": [
        {
            "optin_name": "Hello",
            "optin_label": "Hello Hello",
            "telemetry_id": "749"
        }
    ],
    "voucher_telemetry_id": "",
    "voucher_template_name": "",
    "voucher_email_subject": "",
    "voucher_hero_image": false,
    "voucher_heading": "",
    "voucher_description": null,
    "voucher_generate_code": true,
    "voucher_manual_code": "",
    "voucher_information": null,
    "voucher_terms": null,
    "terms_and_conditions_label": "I accept the terms and conditions",
    "terms_and_conditions": "",
    "thank_you_screen_title": "Thanks",
    "thank_you_screen_body": "<p>Thanks</p>\n",
    "hide_widget_from_page": false
}
```

</details>

### Ticket

Available on: sales pages

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "ticket",
    "ticket_description": "About the ticketone or two lines",
    "ticket_price": "300",
    "ticket_details": "<p>Body copy about what the ticket includes, and sponsors or free things\n\t  </p>\n",
    "button_text": "Buy Ticket",
    "button_link": "https://www.stylist.co.uk/",
    "background_colour": "#d022d6",
    "hide_widget_from_page": false
}
```

</details>

### Video picker

Available on: vide posts

<details>
<summary>Expected return</summary>
<br>

```json
{
    "acf_fc_layout": "video_picker",
    "video_picker_heading": "Video picker",
    "video_posts": [
        {
            "post_title": "Bake it till you make it; the power of pastry with Ravneet Gill",
            "url": "https://staging.stylist.co.uk/entertainment/bake-it-till-you-make-it-the-power-of-pastry-with-ravneet-gill/446002",
            "thumbnails": {
                "url": "https://images-stylist.s3-eu-west-1.amazonaws.com/app/uploads/2020/11/12175948/slh20_video_thumbnails_201112_stay_connected_1600x1080_logo_04_v1.jpg",
                "alt": "",
                "width": 1600,
                "height": 1080,
                "caption": "",
                "description": "",
                "sizes": {},
                "webp_sizes": {}
            },
            "category": "Entertainment",
            "author_name": "Stylist Team",
            "series": false
        }
    ],
    "hide_widget_from_page": false
}
```

</details>

