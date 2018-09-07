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
function renderButton($b) {

	$button = "";

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
   	$class = "uk-button-{$b->button_style->title}";

	if(!empty($b->title)) {
		$button = "<a class='uk-button $class' href='$href' $attr>$b->title</a>";
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
 * Given a group of pages, render a simple <ul> navigation
 *
 * This is here to demonstrate an example of a simple shared function.
 * Usage is completely optional.
 *
 * @param PageArray $items
 *
 */
function renderNav(PageArray $items) {

	if(!$items->count()) return;

	echo "<ul class='nav' role='navigation'>";

	// cycle through all the items
	foreach($items as $item) {

		// render markup for each navigation item as an <li>
		if($item->id == wire('page')->id) {
			// if current item is the same as the page being viewed, add a "current" class to it
			echo "<li class='current' aria-current='true'>";
		} else {
			// otherwise just a regular list item
			echo "<li>";
		}

		// markup for the link
		echo "<a href='$item->url'>$item->title</a> ";

		// if the item has summary text, include that too
		if($item->summary) echo "<div class='summary'>$item->summary</div>";

		// close the list item
		echo "</li>";
	}

	echo "</ul>";
}


/**
 * Given a group of pages render a tree of navigation
 *
 * @param Page|PageArray $items Page to start the navigation tree from or pages to render
 * @param int $maxDepth How many levels of navigation below current should it go?
 *
 */
function renderNavTree($items, $maxDepth = 3) {

	// if we've been given just one item, convert it to an array of items
	if($items instanceof Page) $items = array($items);

	// if there aren't any items to output, exit now
	if(!count($items)) return;

	// $out is where we store the markup we are creating in this function
	// start our <ul> markup
	echo "<ul class='nav nav-tree' role='navigation'>";

	// cycle through all the items
	foreach($items as $item) {

		// markup for the list item...
		// if current item is the same as the page being viewed, add a "current" class and
		// visually hidden text for screen readers to it
		if($item->id == wire('page')->id) {
			echo "<li class='current' aria-current='true'><span class='visually-hidden'>Current page: </span>";
		} else {
			echo "<li>";
		}

		// markup for the link
		echo "<a href='$item->url'>$item->title</a>";

		// if the item has children and we're allowed to output tree navigation (maxDepth)
		// then call this same function again for the item's children
		if($item->hasChildren() && $maxDepth) {
			renderNavTree($item->children, $maxDepth-1);
		}

		// close the list item
		echo "</li>";
	}

	// end our <ul> markup
	echo "</ul>";
}
