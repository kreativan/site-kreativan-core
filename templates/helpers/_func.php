<?php
/**
 * Shared functions used by the beginner profile
 *
 * This file is included by the _init.php file, and is here just as an example.
 * You could place these functions in the _init.php file if you prefer, but keeping
 * them in this separate file is a better practice.
 *
 *	@method loadJS($js)
 *  @method loadCSS($js)
 *  @method renderPagination($obj = "", $align = "left")  
 *  @method renderBreadcrumb($align = "center")
 *  @method renderButton($b)
 *  @method renderMenu($menu, $class = "")
 *	@method renderAlert()
 *
*/

// load js
function loadJS($js) {
    wire('config')->scripts->add($js);
}
// load css
function loadCSS($css) {
    wire('config')->styles->add($css);
}

/**
 *  Pagination
 *  @var obj  page array
 *  @example  renderPagination($page->children());
 *
 */
function renderPagination($obj = "", $align = "left") {

    if($obj && $obj != "") {

        $pagination = $obj->renderPager(array(
            'nextItemLabel'                 => "Next",
            'previousItemLabel'             => "Prev",
            'nextItemClass'                 => "pagination-next",
            'previousItemClass'             => "pagination-prev",
            'lastItemClass'                 => "pagination-last",
            'currentItemClass'              => "uk-active",
            'listMarkup'                    => "<ul class='uk-pagination uk-flex-$align'>{out}</ul>",
            'itemMarkup'                    => "<li class='{class}'>{out}</li>",
            'linkMarkup'                    => "<a href='{url}'><span>{out}</span></a>"
        ));

        if($pagination && $pagination != "") {
            return $pagination;
        }

    }

}

/**
 *  Breadcrumb
 *	@param align
 *  @example renderBreadcrumb("center");
 *
 */

function renderBreadcrumb($align = "center") {

    $breadcrumb = "<ul class='uk-breadcrumb uk-flex-$align uk-margin uk-visible@m'>";
        foreach(wire("page")->parents() as $item) {
            $breadcrumb .= "<li><a href='$item->url'>$item->title</a></li>";
        }
        $breadcrumb .= "<li class='uk-active'><span>".wire('page')->title."</span></li>";
    $breadcrumb .= "</ul>";

    return $breadcrumb;
}

/**
 *  Button
 *
 *	@param b - fieldset object	
 *	@example renderButton($page->button)	
 *
 */
function renderButton($b, $class = "") {

	$button = "";
	
	$class = !empty($class) ? $class : "";

   	// attributes
   	$attr = "";
   	$attr .= ($b->link_attr[1]) ? " target='_blank'" : "";
   	$attr .= ($b->link_attr[2]) ? " rel='nofollow'" : "";
   	$attr .= " title='$b->title'";

   	// href
   	$href = "#";
   	if($b->link_type == '2') {
	   $href = $b->select_page->url;
   	} elseif($b->link_type == '1') {
	   $href = $b->link;
   	}

   	// style
   	$style = "uk-button-{$b->button_style->title}";

	if(!empty($b->title)) {
		$button = "<a class='uk-button $style $class' href='$href' $attr>$b->title</a>";
	}

   	return $button;

}

/**
 *  Menu - Nav
 *
 *  @param menu repeater
 *  @param class str = uikit nav class
 *
 */
function renderMenu($menu, $class = "") {

    $menu_nav = "";
    $menu_nav .= "<ul class='uk-nav $class'>";
        foreach($menu as $nav) {

            $active_cls = "";

            // attributes
            $attr = "";
            $attr .= ($nav->link_attr[1]) ? " target='_blank'" : "";
            $attr .= ($nav->link_attr[2]) ? " rel='nofollow'" : "";

            // href
            $href = "#";
            if($nav->link_type->title == "default") {

                $href = $nav->link;

            } elseif($nav->link_type->title == "page" && !empty($nav->select_page)) {

                $href = $nav->select_page->url;
				
                $active_cls = (wire('page')->id == $nav->select_page->id) ? "class='uk-active'" : "";

            }

            $menu_nav .= "<li {$active_cls}><a href='$href' $attr>$nav->title</a></li>";

        }
    $menu_nav .= "</ul>";

    return $menu_nav;

}

/**
 *  Display UIkit alert based on @var status and @var alert session
 *  @example renderAlert()
 *  or with custom vars
 *  @example("my_status", "my_alert");
 *
 */
function renderAlert($status = "status", $alert = "alert") {

    if(wire("session")->get($alert)) {

        $this_status = wire("session")->get($status);
        $this_alert  = wire("session")->get($alert);

        wire("session")->remove($status);
        wire("session")->remove($alert);

        $ukAlert = "
            <div class='uk-alert-$this_status' uk-alert>
                <a class='uk-alert-close' uk-close></a>
                <p>$this_alert</p>
            </div>
        ";

        return $ukAlert;

    }

}