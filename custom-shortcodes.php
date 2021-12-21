<?php
    function eccent_showdocs_function() {
        $result = '<div class="eccent_document_links">';

        $the_query = new WP_Query( array('posts_per_page'=>15,
            'post_type'=>'meeting_document',
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1) 
        );
        
        while ($the_query -> have_posts()) {
            $the_query->the_post();
            $the_id = get_the_ID();
            $the_title = get_the_title();
            $the_dlink = get_field("document_file", $the_id);
            $the_date = get_the_date();


            $result .= '<p><a href="' . $the_dlink . '" target="_blank">' . $the_title . '</a><span>' . 
                $the_date . 
            '</span></p>';
        } 
        
        $result .= '<div class="eccent_document_pagination">';
        $big = 999999999; // need an unlikely integer
        $result .= paginate_links( array(
        'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $the_query->max_num_pages,
        'prev_text'    => __('<'),
        'next_text'    => __('>')
        ));
        $result .= '</div></div>';

        wp_reset_postdata();
        return($result);
    }

    add_shortcode('showdocs', 'eccent_showdocs_function');

    function eccent_showmeets_function() {
        $result = '<div class="eccent_meetings">';

        $terms = get_terms(array(
            "taxonomy" => "meeting_year",
            'orderby'    => 'name',
            'order'      => 'DESC'
        ));

        foreach($terms as $term) {
            $posts = get_posts(["post_type" => "meeting", "meeting_year" => $term->name, "orderby" => "date", "order" => "ASC", "numberposts" => -1]);
            $posts_count = count($posts);
            $posts_output = '';

            if($posts_count > 0) {
                foreach($posts as $post) {
                    $post_id = $post->ID;
                    $post_month = get_field("meeting_month", $post_id);

                    $post_agenda = get_field("meeting_agenda", $post_id);
                    $post_agenda_docname = $post_agenda['document_name'];
                    $post_agenda_doclink = $post_agenda['document_file'];
                    $post_agenda_output = '';

                    if(empty($post_agenda_docname) == false && isset($post_agenda_docname) && $post_agenda_docname != "") {
                        if(empty($post_agenda_doclink) == false && isset($post_agenda_doclink) && $post_agenda_doclink != "") {
                            $post_agenda_output = '<a href="' . $post_agenda_doclink . '" target="_blank">' . $post_agenda_docname . '</a>';
                        } else {
                            $post_agenda_output = $post_agenda_docname;
                        }
                    }

                    $post_minutes = get_field("meeting_minutes", $post_id);
                    $post_minutes_docname = $post_minutes['document_name'];
                    $post_minutes_doclink = $post_minutes['document_file'];
                    $post_minutes_output = '';

                    if(empty($post_minutes_docname) == false && isset($post_minutes_docname) && $post_minutes_docname != "") {
                        if(empty($post_minutes_doclink) == false && isset($post_minutes_doclink) && $post_minutes_doclink != "") {
                            $post_minutes_output = '<a href="' . $post_minutes_doclink . '" target="_blank">' . $post_minutes_docname . '</a>';
                        } else {
                            $post_minutes_output = $post_minutes_docname;
                        }
                    }

                    $posts_output .= '<div class="eccent_meetings_row"><ul>
                        <li>' . $post_month . '</li>
                        <li>' . $post_agenda_output . '</li>
                        <li>'. $post_minutes_output . '</li>
                    </ul></div>';
                }
    
                $result .= '<div class="eccent_meetings_yearblock">
                <div class="eccent_meetings_row titlerow"><ul>
                  <li>' . $term->name . '</li>
                  <li>Agenda</li>
                  <li>Minutes</li>
                </ul></div>' . 
                    $posts_output . 
                '</div>';
            }
            
        }
        
        $result .= '</div>';
        return($result);
    }

    add_shortcode('showmeets', 'eccent_showmeets_function');
?>