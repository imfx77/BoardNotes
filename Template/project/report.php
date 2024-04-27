<?php

print $this->asset->css('plugins/BoardNotes/Assets/css/style.css');
print $this->asset->js('plugins/BoardNotes/Assets/js/boardnotes.js');
print $this->asset->js('plugins/BoardNotes/Assets/js/load_report.js');

// evaluate optionShowCategoryColors option from session
if (!array_key_exists('boardnotesShowCategoryColors', $_SESSION)) {
    $_SESSION['boardnotesShowCategoryColors'] = false;
}
$optionShowCategoryColors = $_SESSION['boardnotesShowCategoryColors'];
// evaluate optionShowAllDone option from session
if (!array_key_exists('boardnotesShowAllDone', $_SESSION)) {
    $_SESSION['boardnotesShowAllDone'] = false;
}
$optionShowAllDone = $_SESSION['boardnotesShowAllDone'];

// session_vars (hidden reference for options)
print '<div id="session_vars';
print '" data-optionShowCategoryColors="';
print $optionShowCategoryColors ? 'true' : 'false';
print '" data-optionShowAllDone="';
print $optionShowAllDone ? 'true' : 'false';
print '" class="hideMe">';
print '</div>';

?>

<table class="tableReport">
<thead class="theadReport">
<tr>
<th class="thReport thReportNr">#</th>
<th class="thReport"><?= t('Information') ?></th>
<th class="thReport thReportStatus"><?= t('Status') ?></th>
</tr>
</thead>
<tbody>

<?php

$num = "1";

foreach ($data as $u) {
    print '<tr class="trReport" id="trReportNr' . $num . '">';

    print '<td class="tdReport tdReportNr">';
     // Hide button
    print '<button id="reportHide" class="reportHide"';
    print ' data-id="' .  $num . '"';
    print '>';
    print '<i class="fa fa-minus-square-o" style="color:#CCCCCC" aria-hidden="true"';
    print ' title="' . t('BoardNotes_REPORT_HIDE_ROW') . '">';
    print '</i>';
    print '</button>';
    // Report #
    print '<span class="fa-stack fa-lg">';
    print '<i class="fa fa-circle-thin fa-stack-2x"></i>';
    print '<i class="fa fa-inverse fa-stack-1x">' .  $num . '</i>';
    print '</span>';
    print '</td>';

    // Report Info
    print '<td class="tdReport tdReportInfo">';

    // Category label
    print '<label class="catLabel"';
    print ' id="noteCatLabel-P' . $u['project_id'] . '-' . $num . '"';
    print ' data-id="' . $num . '"';
    print ' data-project="' . $u['project_id'] . '"';
    print '>';
    print $u['category'];
    print '</label>';

    // Note title label
    print '<label id="reportTitleLabel-P' . $u['project_id'] . '-' . $num . '"';
    if ($u['is_active'] == "0") {
        print ' class="reportTitleLabel reportTitle noteDoneText">';
    } else {
        print ' class="reportTitleLabel reportTitle">';
    }
    print $u['title'];
    print '</label>';

    // Note details
    if (!empty($u['description'])) {
        print '<div id="noteDetails-P' . $u['project_id'] . '-' . $num . '"';
        print ' class="details reportDetails ui-corner-all">';

        print '<span id="noteMarkdownDetails-P' . $u['project_id'] . '-' . $num . '"';
        if ($u['is_active'] == "0") {
            print ' class="markdown markdownReportDetails reportTitle noteDoneMarkdown"';
        } else {
            print ' class="markdown markdownReportDetails reportTitle"';
        }
        print '>';
        print $this->helper->text->markdown($u['description']);
        print '</span>';

        print '</div>';
    }

    print '</td>'; // report info

    print '<td class="tdReport tdReportStatus reportTitle">';

    if ($u['is_active'] == "2") {
        print '<i class="fa fa-spinner fa-pulse" aria-hidden="true"></i>';
    }
    //if ($u['is_active'] == "1") {
    //    print '<i class="fa fa-circle-thin" aria-hidden="true"></i>';
    //}
    if ($u['is_active'] == "0") {
        print '<i class="fa fa-check" aria-hidden="true"></i>';
    }

    print '</td>';

    print '</tr>';

    // #
    $num++;
}

?>

</tbody>
</table>
