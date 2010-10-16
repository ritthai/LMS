<?php
/**
    Given:  pagetitle,
            actions,
            list :  (id,
                    comment_id,
                    user_name,
                    status,
                    creation_timestamp)
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

        <table>
            <tr>
                <th>id</th>
                <th>comment_id</th>
                <th>user_name</th>
                <th>status</th>
                <th>creation_timestamp</th>
            </tr>
<?php   foreach($args['list'] as $elem) {   ?>
            <tr>
                <td><a href="<?php echo $args['actions']['show']->getLink(array('id'=>$elem['id'])); ?>">
                        <?php echo $elem['id']; ?>
                    </a>
                </td>
                <td><?php echo $elem['comment_id']; ?></td>
                <td><?php echo $elem['user_name']; ?></td>
                <td><?php echo $elem['status'] == 0 ? 'open' : 'closed'; ?></td>
                <td><?php echo $elem['creation_timestamp']; ?></td>
            </tr>
<?php   }   ?>
		</table>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>
