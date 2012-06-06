# Image Resize

An application to dynamically resize a user uploaded file to any given width / height.
This app creates a file matching the exact request URI meaning once a scaled image is
created all subsequent requests for it are handled by Apache - jaoss is never even invoked.

## Required Settings

```
[images]
scaled=PROJECT_ROOT"public/uploads/scaled/"
max_width=(int)
max_height=(int)
```

## Usage

Simply embed an image tag with the appropriate source attribute.

To scale width only:

```<img src="/uploads/scaled/123/image.png" />```

To scale width and height:

```<img src="/uploads/scaled/123x123/image.png" />```

## Tests

```phpunit apps/image_resize/tests``` - 100%
