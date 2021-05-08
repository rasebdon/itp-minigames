<?php

/**
 * Static class that holds forum rendering functions
 */
class PostRenderer
{

    /** @var PostRenderer */
    public static $instance;

    function __construct()
    {
        if (!isset($_GET['action']) || !isset($_GET['id']))
            return;

        switch ($_GET['action']) {
            case "post":
                $this->RenderPost($_GET['id']);
                break;
        }
    }

    function renderPost($postid)
    {
        $post = ForumService::$instance->getPost($postid);
        var_dump($post);
    }
}

PostRenderer::$instance = new PostRenderer();
