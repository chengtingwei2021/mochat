<?php

declare(strict_types=1);
/**
 * This file is part of MoChat.
 * @link     https://mo.chat
 * @document https://mochat.wiki
 * @contact  group@mo.chat
 * @license  https://github.com/mochat-cloud/mochat/blob/master/LICENSE
 */
namespace MoChat\App\Medium\Task;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use MoChat\App\Medium\Contract\MediumContract;
use MoChat\App\Medium\Logic\Medium;
use MoChat\App\WorkMessage\Contract\WorkMessageConfigContract;

/**
 * @Crontab(name="mediaIdUpdate", rule="*\/5 * * * *", callback="execute", singleton=true, memo="素材库media_id更新")
 */
class MediaIdUpdate
{
    /**
     * @Inject
     * @var WorkMessageConfigContract
     */
    private $corConfigClient;

    /**
     * @Inject
     * @var MediumContract
     */
    private $mediaService;

    /**
     * @Inject
     * @var Medium
     */
    private $mediaLogic;

    public function execute(): void
    {
        // 循环企业
        $corpConfig = $this->corConfigClient->getWorkMessageConfigsByDoneStatus(['id', 'corp_id']);

        foreach ($corpConfig as $corp) {
            $mediumIds = $this->mediaService->getMediaByUpdatingMediaId($corp['corpId'], ['id']);
            if (empty($mediumIds)) {
                continue;
            }
            $this->mediaLogic->getWxMediumId(array_column($mediumIds, 'id'), $corp['corpId']);
        }
    }
}
