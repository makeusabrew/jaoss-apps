<?php

PathManager::loadPaths(
    array("/uploads/scaled/(?P<width>\d+)x(?P<height>\d+)/(?P<path>.+)", "resize_image"),
    array("/uploads/scaled/(?P<width>\d+)/(?P<path>.+)", "resize_image")
);
