<?php

/**
 * Class Wordpress
 */
class Wordpress
{
    /**
     * @var array
     */
    private $header_styles;

    /**
     * @var array
     */
    private $header_scripts;

    /**
     * @var array
     */
    private $body_scripts;

    /**
     *
     */
    public function __construct()
    {
        global $s_wp_css_js;
        $s_wp_css_js = false;

        $this->setHeaderStyles( array() );
        $this->setHeaderScripts( array() );
        $this->setBodyScripts( array() );
    }

    /**
     * Set BodyScripts
     *
     * @param array $body_scripts
     *
     * @return Wordpress
     */
    public function setBodyScripts( $body_scripts = NULL )
    {
        $this->body_scripts = $body_scripts;

        return $this;
    }

    /**
     * Get BodyScripts
     *
     * @return array
     */
    public function getBodyScripts()
    {
        return $this->body_scripts;
    }

    /**
     * Set HeaderScripts
     *
     * @param array $header_scripts
     *
     * @return Wordpress
     */
    public function setHeaderScripts( $header_scripts = NULL )
    {
        $this->header_scripts = $header_scripts;

        return $this;
    }

    /**
     * Get HeaderScripts
     *
     * @return array
     */
    public function getHeaderScripts( $render = false )
    {
        if( empty( $this->header_scripts ) )
        {
            $this->parseWpHead();
        }

        if( $render ) {
            return implode( PHP_EOL, $this->header_scripts );
        }

        return $this->header_scripts;
    }

    /**
     * Set HeaderStyles
     *
     * @param array $header_styles
     *
     * @return Wordpress
     */
    public function setHeaderStyles( $header_styles = NULL )
    {
        $this->header_styles = $header_styles;

        return $this;
    }

    /**
     * Get HeaderStyles
     *
     * @return array
     */
    public function getHeaderStyles( $render = false )
    {
        if( empty( $this->header_styles ) ) {
            $this->parseWpHead();
        }

        if( $render ) {
            return implode( PHP_EOL, $this->header_styles );
        }

        return $this->header_styles;
    }

    public function parseWpHead()
    {
        global $s_wp_css_js;
        $s_wp_css_js = true;
        ob_start();
        wp_head();
        $output = ob_get_clean();
        $s_wp_css_js = false;

        $doc = new DOMDocument();
        @$doc->loadHtml( $output );

        $styles = $doc->getElementsByTagName( 'link' );
        $scripts = $doc->getElementsByTagName( 'script' );

        foreach( $styles as $style )
        {
            $href = $style->getAttribute( 'href' );
            $rel = $style->getAttribute( 'rel' );
            $type = $style->getAttribute( 'type' );
            $title = $style->getAttribute( 'title' );
            $this->header_styles[] = '<link title="' . $title . '" rel="' . $rel . '" type="' . $type . '" href="' . $href . '" />';
        }

        foreach( $scripts as $script )
        {
            $src = $script->getAttribute( 'src' );
            $type = $script->getAttribute( 'type' );
            $this->header_scripts[] = '<script type="' . $type . '" src="' . $src . '"></script>';
        }
    }

    /**
     * Retrieve the site url for a given site.
     *
     * Returns the 'site_url' option with the appropriate protocol, 'https' if
     * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
     * overridden.
     *
     * @package WordPress
     * @since 3.0.0
     *
     * @param int $blog_id (optional) Blog ID. Defaults to current blog.
     * @param string $path Optional. Path relative to the site url.
     * @param string $scheme Optional. Scheme to give the site url context. Currently 'http', 'https', 'login', 'login_post', 'admin', or 'relative'.
     * @return string Site url link with optional path appended.
     */
    function get_site_url( $blog_id = null, $path = '', $scheme = null ) {
        $url = "{{ url( 'wordpress_index' ) }}";
        if( !empty( $path ) ) {
            if(substr($path,0,1) == '/') {
                $url .= substr( $path, 1 );
            } else {
                $url .= $path;
            }
        }
        return str_replace( 'app_dev.php/', '', $url );
    }

    /**
     * Retrieve the url to the admin area for a given site.
     *
     * @package WordPress
     * @since 3.0.0
     *
     * @param int $blog_id (optional) Blog ID. Defaults to current blog.
     * @param string $path Optional path relative to the admin url.
     * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
     * @return string Admin url link with optional path appended.
     */
    function get_admin_url( $blog_id = null, $path = '', $scheme = 'admin' ) {
        $url = "{{ url( 'admin_wordpress_index' ) }}";
        if( !empty( $path ) ) {
            if(substr($path,0,1) == '/') {
                $url .= substr( $path, 1 );
            } else {
                $url .= $path;
            }
        }
        return str_replace( 'app_dev.php/', '', $url );
    }

    public function wp_plugin_directory_constants()
    {
        defined( 'SYMFONY_WP_SITE_URL' ) || define( 'SYMFONY_WP_SITE_URL', get_option('siteurl'));
        echo SYMFONY_WP_SITE_URL;
    }

    /**
     * Retrieve the home url for a given site.
     *
     * Returns the 'home' option with the appropriate protocol, 'https' if
     * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
     * overridden.
     *
     * @package WordPress
     * @since 3.0.0
     *
     * @param  int $blog_id   (optional) Blog ID. Defaults to current blog.
     * @param  string $path   (optional) Path relative to the home url.
     * @param  string $scheme (optional) Scheme to give the home url context. Currently 'http', 'https', or 'relative'.
     * @return string Home url link with optional path appended.
     */
    function get_home_url( $blog_id = null, $path = '', $scheme = null ) {
        return $this->get_site_url( $blog_id, $path, $scheme );
    }
    /**
     * Return a shortlink for a post, page, attachment, or blog.
     *
     * This function exists to provide a shortlink tag that all themes and plugins can target. A plugin must hook in to
     * provide the actual shortlinks. Default shortlink support is limited to providing ?p= style links for posts.
     * Plugins can short-circuit this function via the pre_get_shortlink filter or filter the output
     * via the get_shortlink filter.
     *
     * @since 3.0.0.
     *
     * @param int $id A post or blog id. Default is 0, which means the current post or blog.
     * @param string $context Whether the id is a 'blog' id, 'post' id, or 'media' id. If 'post', the post_type of the post is consulted. If 'query', the current query is consulted to determine the id and context. Default is 'post'.
     * @param bool $allow_slugs Whether to allow post slugs in the shortlink. It is up to the plugin how and whether to honor this.
     * @return string A shortlink or an empty string if no shortlink exists for the requested resource or if shortlinks are not enabled.
     */
    function wp_get_shortlink($id = 0, $context = 'post', $allow_slugs = true) {
        // Allow plugins to short-circuit this function.
        $shortlink = apply_filters('pre_get_shortlink', false, $id, $context, $allow_slugs);
        if ( false !== $shortlink )
            return $shortlink;

        global $wp_query;
        $post_id = 0;
        if ( 'query' == $context && is_singular() ) {
            $post_id = $wp_query->get_queried_object_id();
            $post = get_post( $post_id );
        } elseif ( 'post' == $context ) {
            $post = get_post( $id );
            if ( ! empty( $post->ID ) )
                $post_id = $post->ID;
        }

        $shortlink = '';

        // Return p= link for all public post types.
        if ( ! empty( $post_id ) ) {
            $post_type = get_post_type_object( $post->post_type );
            if ( $post_type->public )
                $shortlink = home_url('?p=' . $post_id);
        }

        return apply_filters('get_shortlink', $shortlink, $id, $context, $allow_slugs);
    }

    function esc_url( $url = null, $site_url = null ) {
        $ext = pathinfo( $url, PATHINFO_EXTENSION );
        $file = pathinfo( $url, PATHINFO_FILENAME );

        $omit_ext = array( 'css', 'js' );
        $omit_file = array( 'xmlrpc', '?feed=rss2' );

        global $s_wp_css_js;
        if( $s_wp_css_js === true || strstr( $ext, 'php' ) || strstr( $ext, 'css' ) || strstr( $ext, 'js' ) ) {
            return $url;
        }

        $url = str_replace( $site_url . '/', $this->get_site_url( ), $url );
        $url = str_replace( $site_url . '', $this->get_site_url( ), $url );

        return $url;
    }

    public function convertUrl( $url = null, $debug = false )
    {
        if( empty( $url ) ) return $url;
        $home_url = site_url();

        if( $home_url != $this->get_site_url() ) {
            $url = str_replace( $home_url . '/', $this->get_site_url( ), $url );
            $url = str_replace( $home_url . '', $this->get_site_url( ), $url );
        }

        if( $debug ) {
            echo $url . '<br>';
            echo '[' . substr( $url, 0, 12 ) . ']';
        }

        // for those relative links
        if( substr( $url, 0, 12 )  == '/core_sys_wp' )
        {
            $url = preg_replace( '/\/core_sys_wp[\/]+/', $this->get_site_url(), $url );
        }

        // sometimes home url or site url is not available
        // so lets search manually and replace
        $matches = array();
        if( preg_match( '/\/core_sys_wp\/(.*)/', $url, $matches ) ) {
            $url = $this->get_site_url() . ( isset( $matches[1] ) ? $matches[1] : null );
        }

        return $url;
    }

    public function get_wp_widget_meta( $args = null, $instance = null )
    {
        ob_start();
        bloginfo('rss2_url');
        $rss2_url = ob_get_clean();
        $rss2_url = $this->convertUrl( $rss2_url );

        ob_start();
        bloginfo('comments_rss2_url');
        $comments_rss2_url = ob_get_clean();
        $comments_rss2_url = $this->convertUrl( $comments_rss2_url );

        ?>
        <?php wp_register(); ?>
        <li><?php wp_loginout(); ?></li>
        <li><a href="<?php echo $rss2_url; ?>" target="_blank" title="<?php echo esc_attr(__('Syndicate this site using RSS 2.0')); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
        <li><a href="<?php echo $comments_rss2_url; ?>" target="_blank" title="<?php echo esc_attr(__('The latest comments to all posts in RSS')); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
        <?php
    }

    /**
     * Display the Registration or Admin link.
     *
     * Display a link which allows the user to navigate to the registration page if
     * not logged in and registration is enabled or to the dashboard if logged in.
     *
     * @since 1.5.0
     * @uses apply_filters() Calls 'register' hook on register / admin link content.
     *
     * @param string $before Text to output before the link (defaults to <li>).
     * @param string $after Text to output after the link (defaults to </li>).
     * @param boolean $echo Default to echo and not return the link.
     * @return string|null String when retrieving, null when displaying.
     */
    function wp_register( $before = '<li>', $after = '</li>', $echo = true ) {

        if ( ! is_user_logged_in() ) {
            if ( get_option('users_can_register') )
                $link = $before . '<a href="' . $this->convertUrl( wp_registration_url() ) . '">' . __('Register') . '</a>' . $after;
            else
                $link = '';
        } else {
            $link = $before . '<a href="' . $this->convertUrl( admin_url() ) . '">' . __('Site Admin') . '</a>' . $after;
        }

        if ( $echo )
            echo apply_filters('register', $link);
        else
            return apply_filters('register', $link);
    }

    /**
     * Display the Log In/Out link.
     *
     * Displays a link, which allows users to navigate to the Log In page to log in
     * or log out depending on whether they are currently logged in.
     *
     * @since 1.5.0
     * @uses apply_filters() Calls 'loginout' hook on HTML link content.
     *
     * @param string $redirect Optional path to redirect to on login/logout.
     * @param boolean $echo Default to echo and not return the link.
     * @return string|null String when retrieving, null when displaying.
     */
    function wp_loginout($redirect = '', $echo = true) {
        if ( ! is_user_logged_in() )
            $link = '<a href="' . $this->convertUrl( wp_login_url($redirect) ) . '">' . __('Log in') . '</a>';
        else
            $link = '<a href="' . $this->convertUrl( wp_logout_url($redirect) ) . '">' . __('Log out') . '</a>';

        if ( $echo )
            echo apply_filters('loginout', $link);
        else
            return apply_filters('loginout', $link);
    }

    function get_comment_reply_link($args = array(), $comment = null, $post = null, $login_text = null, $respond_id = null, $add_below = null, $reply_text = null ) {
        $link = '';
        if ( get_option('comment_registration') && ! is_user_logged_in() ) {
            $link = '<a rel="nofollow" class="comment-reply-login" href="' . $this->convertUrl( wp_login_url( get_permalink() ) ) . '">' . $login_text . '</a>';
        } else {
            $link = "<a class='comment-reply-link' href='" . $this->convertUrl( add_query_arg( 'replytocom', $comment->comment_ID ) ) . "#" . $respond_id . "' onclick='return addComment.moveForm(\"$add_below-$comment->comment_ID\", \"$comment->comment_ID\", \"$respond_id\", \"$post->ID\")'>$reply_text</a>";
        }

        return $link;
    }
}