<?php

/**
 * Class xpanTableGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class xpanSortingTableGUI extends ilTable2GUI
{

    const TBL_ROW_TEMPLATE_NAME = "tpl.sorting_row.html";
    const TBL_ROW_TEMPLATE_DIR = "/templates/table_rows/";
    const JS_FILES_TO_EMBED
        = [
            "/js/xoctWaiter.js",
            "/js/sortable.js",
        ];
    const CSS_FILES_TO_EMBED
        = [
            "/templates/default/xoctWaiter.css",
            "/templates/default/sorting_table.css",
        ];


    /**
     * xpanTableGUI constructor.
     *
     * @param                 $a_parent_obj
     * @param ilPanoptoPlugin $pl
     * @param                 $sessions
     */
    public function __construct($a_parent_obj, $pl, $sessions)
    {
        parent::__construct($a_parent_obj);
        $plugin_dir = $pl->getDirectory();

        $this->initColumns($pl);
        $this->setRowTemplate($pl->getDirectory() . self::TBL_ROW_TEMPLATE_DIR . self::TBL_ROW_TEMPLATE_NAME, $plugin_dir);

        $this->setExternalSorting(true);
        $this->setExternalSegmentation(true);
        $this->setShowRowsSelector(true);

        $this->applyFiles($plugin_dir);
        $this->parseData($sessions);
    }


    /**
     * @param ilPanoptoPlugin $pl
     */
    protected function initColumns($pl)
    {
        $this->addColumn("", 'move_icon');
        $this->addColumn($pl->txt('content_thumbnail'));
        $this->addColumn($pl->txt('content_title'));
        $this->addColumn($pl->txt('content_description'));
    }


    protected function fillRow($session)
    {
        $this->tpl->setVariable("VAL_THUMBNAIL",
            'https://' . xpanConfig::getConfig(xpanConfig::F_HOSTNAME) . $session->getThumbUrl()
        );
        $this->tpl->setVariable("VAL_TITLE", $session->getName());
        $this->tpl->setVariable("VAL_DESCRIPTION", $session->getDescription());
        $this->tpl->setVariable("VAL_MID", $session->getId());
    }


    /**
     * @param string $plugin_dir
     */
    protected function applyFiles($plugin_dir)
    {
        global $DIC;
        $main_tpl = $DIC->ui()->mainTemplate();

        foreach (self::JS_FILES_TO_EMBED as $pathSuffix) {
            $main_tpl->addJavaScript($plugin_dir . $pathSuffix);
        }

        foreach (self::CSS_FILES_TO_EMBED as $pathSuffix) {
            $main_tpl->addCss($plugin_dir . $pathSuffix);
        }

        $base_link = $this->ctrl->getLinkTarget($this->parent_obj, '', '', true);
        $main_tpl->addOnLoadCode('PanoptoSorter.init("' . $base_link . '");');
        $main_tpl->addOnLoadCode('xoctWaiter.init("waiter");');
    }


    protected function parseData($sessions)
    {
        $this->setData($sessions["sessions"]);
    }
}