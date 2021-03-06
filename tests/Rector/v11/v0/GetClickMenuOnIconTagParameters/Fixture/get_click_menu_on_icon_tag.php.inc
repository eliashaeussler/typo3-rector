<?php

namespace Ssch\TYPO3Rector\Tests\Rector\v11\v0\GetClickMenuOnIconTagParameters\Fixture;

use TYPO3\CMS\Backend\Utility\BackendUtility;

BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo', '', '', '');
BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo', '', '', '', false);
BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo', '', '', '', true);
$returnTagParameters = false;
BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo', '', '', '', $returnTagParameters);
$returnTagParameters = true;
BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo', '', '', '', $returnTagParameters);

?>
-----
<?php

namespace Ssch\TYPO3Rector\Tests\Rector\v11\v0\GetClickMenuOnIconTagParameters\Fixture;

use TYPO3\CMS\Backend\Utility\BackendUtility;

BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo');
BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo');
BackendUtility::getClickMenuOnIconTagParameters('pages', 1, 'foo');
$returnTagParameters = false;
BackendUtility::wrapClickMenuOnIcon('pages', 1, 'foo');
$returnTagParameters = true;
BackendUtility::getClickMenuOnIconTagParameters('pages', 1, 'foo');

?>
