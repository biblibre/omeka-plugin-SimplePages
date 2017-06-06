<?php queue_css_file('simple-pages'); ?>

<?php
$head = array(
    'bodyclass' => 'simple-pages primary',
    'title' => __('Simple Pages | Translate "%s"', metadata('simple_pages_page', 'title')),
);
echo head($head);
?>

<?php echo flash(); ?>

<a class="add button small green" href="<?php echo url('simple-pages/translation/add', array('page_id' => $simple_pages_page->id)); ?>"><?php echo __('Add a translation'); ?></a>

<table>
    <thead>
        <tr>
            <th><?php echo __('Language'); ?></th>
            <th><?php echo __('Title'); ?></th>
            <th><?php echo __('Text'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $translations = $simple_pages_page->getTranslations(); ?>
        <?php if (!empty($translations)): ?>
            <?php foreach ($translations as $translation): ?>
                <tr>
                    <td class="translation-locale">
                        <span>
                            <?php if (plugin_is_active('LocaleSwitcher')): ?>
                                <?php echo locale_description($translation->locale); ?>
                                (<?php echo $translation->locale; ?>)
                            <?php else: ?>
                                <?php echo $translation->locale; ?>
                            <?php endif; ?>
                        </span>
                        <ul class="action-links">
                            <li><a href="<?php echo record_url($translation, 'edit'); ?>"><?php echo __('Edit'); ?></a></li>
                            <li><a href="<?php echo record_url($translation, 'delete-confirm'); ?>"><?php echo __('Delete'); ?></a></li>
                        </ul>
                    </td>
                    <td class="translation-title">
                        <?php echo $translation->title; ?>
                    </td>
                    <td class="translation-text">
                        <?php echo snippet($translation->text, 0, 200); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3"><?php echo __("There is no translation for this page yet."); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php echo foot(); ?>
