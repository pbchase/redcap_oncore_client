<?php

namespace OnCoreClient\Entity;

use DOMDocument;
use RCView;
use REDCap;
use REDCapEntity\EntityList;

class APILogsList extends EntityList {

    protected function getTableHeaderLabels() {
        $header = parent::getTableHeaderLabels() + ['__view_data' => ''];
        unset($header['id'], $header['updated'], $header['error_msg']);

        $header['created'] = 'Request time';

        return $header;
    }

    protected function buildTableRow($entity) {
        $row = parent::buildTableRow($entity);

        $row['__view_data'] = RCView::button([
            'class' => 'btn btn-info btn-xs',
            'data-toggle' => 'modal',
            'data-target' => '#oncore-data-' . $id,
        ], 'View details');

        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        $data = $entity->getData();

        foreach (['request', 'response'] as $key) {
            if (empty($data[$key])) {
                continue;
            }

            $doc->loadXML($data[$key]);
            $data[$key] = $doc->saveXML();
            $data[$key] = RCView::pre([], REDCap::escapeHtml($data[$key]));
        }

        $data = [
            'request' => $data['request'],
            'response' => $data['response'],
            'error_msg' => $data['error_msg'],
        ];

        include $this->module->getModulePath() . 'templates/data_modal.php';

        return $row;
    }
}
