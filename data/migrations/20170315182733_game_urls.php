<?php

use Phinx\Migration\AbstractMigration;

class GameUrls extends AbstractMigration
{
    protected $gameImages = [
        'aqua-lunch-man'       => '364c9a2dcd31232af6ee96514b7e8c8b.gif',
        'be-bright'            => 'e2426e0504ee73d6dc45c689f5d5f323.gif',
        'drought-out'          => 'a93c9f72b0df38ff63a6d0b686231bf6.gif',
        'happy-fish-face'      => 'f64606d786cb75e0eff7ca5ea5706abf.gif',
        'safety-first'         => '87ee5b0898c7e736e27353adf888fe4d.gif',
        'vr-world'             => '69faa25178b47e20275d19a63c5ff255.gif',
        'litter-bug'           => 'f55a5a39ada3b6b5e88f14298e631725.gif',
        'meerkat-mania'        => '47605ffc74272d540f2aaf083e1748fb.gif',
        'polar-bear'           => 'fb390fecbf002b31bada0644f66fdb9a.gif',
        'printmaster'          => '01a37a9e8707c8cdcc77afe8e611ce47.gif',
        'sea-turtle'           => '88929f4f060b7573c9137d4a67c2f049.gif',
        'tag-it'               => '9edd1259b96b2d676a88c83047af3656.gif',
        'twirl-n-swirl'        => 'dcd11a5f09726c4b0a91586b530c8264.gif',
        'fire'                 => '6b2eab034602dc55d7c32312499bbc71.gif',
        'monarch'              => 'aca58f6b79a7ef1331a5f1f88a26c89b.gif',
        'waterdrop'            => '51861f8a31c9d899d73b38995625ed46.gif',
        'twirling-tower'       => '3890d968d8295e472006d0e8d0787f4a.gif',
        'salad-rain'           => '0a4edeec009c5ac56b1e586014cf002d.gif',
        'reef-builder'         => '19ff5314ce51ea7f4a059f0c07ac4142.gif',
        'pedal-pusher'         => '5e0eab9f9a6b70fc47b7586172f20c09.gif',
        'bloom-boom'           => 'd0341487768a960b6d255a3b25d2bf12.gif',
        'all-about-you'        => '14f629c77736290adc41531d72a6cc54.gif',
        'animal-id'            => '1d30b3302aad1608ad76c4029a4c2d5a.gif',
        'carbon-catcher'       => 'ab894b9d48a225ffdace7215003dd228.gif',
        'skribble'             => '10b58a3fbacaa46203faf65a02f8fbbc.gif',
        'turtle-hurdle'        => '1cf1a4952107ce19e6b8675643a17c5d.gif',
        '3d-world'             => '3d45287ac7d23711d32af8b7c797c2f8.gif',
        'green-team-challenge' => '8254c3f834ced23d71fd1de6c0ae1aad.gif',
        'green-team'           => '8254c3f834ced23d71fd1de6c0ae1aad.gif',
        'waste-busters'        => 'a41a5f14aa880f298a987134849629fe.png',
        'drone-zone'           => '9e9b3bae0f9e8ba512eed9fad35868b2.gif',
    ];

    protected $flipImages = [
        'drought-out'           => [
            'earned'   => '65e520c9fca8531d935967f1ddba7c4b.gif',
            'unearned' => '44622644f9eb3bfce8640649c2dbe3b4.gif',
            'static'   => '9fda7f0ebf5605365e3ad4baab0d45bc.gif',
        ],
        'vr-world'              => [
            'earned'   => '8aa6c7318e65921f4cf5b08e78086104.gif',
            'unearned' => '47717bd52fa77982d368e0e45e2c1367.gif',
            'static'   => '7d8acaab1fc106bb391dbb1a1248fd8c.gif',
        ],
        'meerkat-mania'         => [
            'earned'   => 'da495622e3904ab9b09f873b0f84e9dc.gif',
            'unearned' => '17bf4a5951b9a080d617f5f2090dfa1a.gif',
            'static'   => 'a15e42ad74195bd242655312638abb1e.gif',
        ],
        'monarch'               => [
            'earned'   => 'c4e788fbdd07fc8ee74f63e41d49f1ea.gif',
            'unearned' => '07835df48fd10361ea84a5f7bdb1f0d5.gif',
            'static'   => '1579fe356f574d3bd82d2e308f9b9fc6.gif',
        ],
        'happy-fish-face'       => [
            'earned'   => '8754541563bfc122317e6fecf586b02a.gif',
            'unearned' => '7c11025dd0da39e20298695162878ea4.gif',
            'static'   => '9c3ff4ca0d35413a823f23b6a50a8b28.gif',
        ],
        'fire'                  => [
            'earned'   => 'c4f4d2daf3da27c1cd75c628f26c8e67.gif',
            'unearned' => '89dd8202c6e8d321a22ee9a919afa34a.gif',
            'static'   => 'f99caa5f4d63a9e51af14cc56a4ac855.gif',
        ],
        'future-tech'           => [
            'earned'   => '52cdcefb8e7e1213cd4e7011a23135fd.gif',
            'unearned' => '39dff682e7f2883724d5cca5fae7dad0.gif',
            'static'   => '93fadd284d744197e41885b6580d1d95.gif',
        ],
        'robot-discovery'       => [
            'earned'   => 'a8b37d03762674875b12adb06576e123.gif',
            'unearned' => 'e960c47d44af829024d5c73f4111d9c2.gif',
            'static'   => '27615088dbe764e90eb98221fc323af1.gif',
        ],
        'safety-first'          => [
            'earned'   => 'dc9f2a6c76dcb9a2ecda12030dee4e4e.gif',
            'unearned' => 'a2be3a746ec3414673b48b5da9a72b24.gif',
            'static'   => '5688a4d8aa531f90a85dc1a65ccb1461.gif',
        ],
        'sea-turtle'            => [
            'earned'   => '02513d1b358ca62348056fa790cc131b.gif',
            'unearned' => 'c4fdf16fd4dfc3dd86ac597d90222831.gif',
            'static'   => '0a5e174ad664c593dafcb00aaa5d134e.gif',
        ],
        'animal-id'             => [
            'earned'   => 'bf80cf8927312bc07f4a2d635b66316e.gif',
            'unearned' => '9a328ed10995960b64b80b37f39e77da.gif',
            'static'   => '7c0eed27b091680009af2e4f2e142f92.gif',
        ],
        'be-bright'             => [
            'earned'   => '36ded2bea2d658e4cdf5b03ec74dee5a.gif',
            'unearned' => '63f92723104135acb1ad184e0299815d.gif',
            'static'   => 'b8a31f3999330f7006cc77268d4d249c.gif',
        ],
        'aqua-lunch-man'        => [
            'earned'   => '6f08c8dbd137731b51bbb53fdb1c6a0a.gif',
            'unearned' => '67498e45119594259fca836d896b2ee1.gif',
            'static'   => '30cf526659cf78e7b133126c38ecb29a.gif',
        ],
        'printmaster'           => [
            'earned'   => '5781900c6de008675bc09f3e2e4777a8.gif',
            'unearned' => 'fd79a5aa07706bc929990d49a19117e9.gif',
            'static'   => '683a78c259caabf38a2d159cd2c89103.gif',
        ],
        'all-about-you'         => [
            'earned'   => 'cd78ab13f5ff76db13050c240410b9fc.gif',
            'unearned' => '158fd4beb460f657b82acb764feff1ec.gif',
            'static'   => '9b97b875ce3adac10de5e34563005791.gif',
        ],
        'tag-it'                => [
            'earned'   => 'ebb5488811b648180369a3b6ff6c9c1a.gif',
            'unearned' => 'e5625e1996e2defbe90a551cc47d399f.gif',
            'static'   => '3431b0e395899d667efef26b2076783e.gif',
        ],
        'waste-pro'             => [
            'earned'   => 'b828c3c25b0b7f24a5c09af79a047367.gif',
            'unearned' => '75ca51d4cc382de3b7a1eb16ab7aad06.gif',
            'static'   => '6bad950e41760c9b4e5d7cf1177f2367.gif',
        ],
        'litter-bug'            => [
            'earned'   => 'b06d351351a10c51a17580944cad022d.gif',
            'unearned' => '4225552208d5fa38f28b1019538700af.gif',
            'static'   => 'f647d96033590a86d8667ba0724e0999.gif',
        ],
        'twirl-n-swirl'         => [
            'earned'   => '507666b88d9e7c81dd8761e45601d493.gif',
            'unearned' => '602474f48d8ccb6b417c5d66a2f22e5b.gif',
            'static'   => '34c71b588d696c50b9b61b2f22331ff6.gif',
        ],
        '3d-world'              => [
            'earned'   => '51cbe539a517fe200e3ac54634afe82d.gif',
            'unearned' => '6976580b01d9c6cdfcf88465364c05b3.gif',
            'static'   => 'c4ee3ae7ed75e2fa1eb8b109256926f2.gif',
        ],
        'polar-bear'            => [
            'earned'   => 'e6be7b03b894d711d95dd4d8aa26bb0a.gif',
            'unearned' => '044049385dc8499dc53b808a6e726f48.gif',
            'static'   => '4a3e47e27a7415fb10d09637c0a240c6.gif',
        ],
        'green-team'            => [
            'earned'   => '9063d79b44cf9c4c39ff32e8dae931e0.gif',
            'unearned' => '41cf97dfd52d73f62ad678a6f2c1cc54.gif',
            'static'   => '00a1afcb41b46fffa60b042771374891.gif',
        ],
        'green-team-challenge'  => [
            'earned'   => '9063d79b44cf9c4c39ff32e8dae931e0.gif',
            'unearned' => '41cf97dfd52d73f62ad678a6f2c1cc54.gif',
            'static'   => '00a1afcb41b46fffa60b042771374891.gif',
        ],
        'recycling-champion'    => [
            'earned'   => 'd5fbeea00d04559674ce65e42dbb360a.gif',
            'unearned' => 'b434b943743e6804cbc6ba5926526efd.gif',
            'static'   => '55afd316be2600d967f70aad69569ac5.gif',
        ],
        'priceless-pourer'      => [
            'earned'   => '11509474e4e98b6aa0e3d98a7b73de91.gif',
            'unearned' => '7fcba0d87084cd75e81aa4a14de7c0bf.gif',
            'static'   => '24eaaecd6818b6f1f93bbe8a1e45959b.gif',
        ],
        'fantastic-food-sharer' => [
            'earned'   => '7326131e224d281e90292418758e2e1b.gif',
            'unearned' => 'af4b534b3c0e49eab1ec4a22b314ead1.gif',
            'static'   => '715ffba385a9f7c4a63ace2e4feb44b7.gif',
        ],
        'dynamic-diverter'      => [
            'earned'   => '25586caa61a58b4378f483cd1af1ba51.gif',
            'unearned' => 'e9da048c7d075318386101c672aa645d.gif',
            'static'   => 'e2de3628439f8bc5b2b8d3577443328d.gif',
        ],
        'master-sorter'         => [
            'earned'   => 'd7c67f43016f68916d89abafe88fbdcd.gif',
            'unearned' => '7bee327c9c1ac9fd6830029bc484f956.gif',
            'static'   => '27f759d9631757f5654d8b2a1bdcb2cc.gif',
        ],
        'waste-busters'         => [
            'earned'   => '4bd1ddfa94a551ccd3731f3018e7f49f.gif',
            'unearned' => '4e68a0104f2f66edba34685aa3ca3462.gif',
            'static'   => '42d3b49e1f74b773bd42f730203520d0.gif',
        ],
    ];

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $gameUrl  = $this->getGameUrl();
        $mediaUrl = $this->getMediaUrl();

        $this->changeTablesStepOne();

        $this->updateGameData($gameUrl, $mediaUrl);
//        $this->updateFlipData($mediaUrl);

        $this->changeTablesStepTwo();
    }

    /**
     * Step one allows nulls for some fields to allow migrating the data easier
     */
    protected function changeTablesStepOne()
    {
        $gameTable = $this->table('games');
        if (!$gameTable->hasColumn('uris')) {
            $gameTable->addColumn('uris', 'text', ['null' => true]);
        }

        if (!$gameTable->hasColumn('flags')) {
            $gameTable->addColumn(
                'flags',
                'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::BLOB_LONG, 'null' => true, 'default' => 0]
            );
        }

        if (!$gameTable->hasColumn('sort_order')) {
            $gameTable->addColumn(
                'sort_order',
                'integer',
                ['null' => true, 'default' => 0]
            );
        }

        $gameTable->save();
    }

    /**
     * Step one allows nulls for some fields to allow migrating the data easier
     */
    protected function changeTablesStepTwo()
    {
        $gameTable = $this->table('games');
        if ($gameTable->hasColumn('global')) {
            $gameTable->removeColumn('global');
        }

        if ($gameTable->hasColumn('coming_soon')) {
            $gameTable->removeColumn('coming_soon');
        }

        $gameTable->save();
    }

    /**
     * Updates the game table with data in the new fields
     *
     * @param string $gameUrl  base path to the game server
     * @param string $mediaUrl base path to the media server
     */
    protected function updateGameData($gameUrl, $mediaUrl)
    {
        $games = $this->query('SELECT * FROM games');

        foreach ($games as $gameData) {
            $gameId = $gameData['game_id'] ?? '';

            $thumbUrl  = $mediaUrl . '/titles/default-animated.gif';
            $bannerUrl = $mediaUrl . '/titles/default-18-5.jpg';

            if (isset($this->gameImages[$gameId])) {
                $thumbUrl  = $mediaUrl . '/f/' . $this->gameImages[$gameId];
                $bannerUrl = $mediaUrl . '/titles/18-5/' . $gameId . '.jpg';
            }

            $uris = str_replace('"', '\"', json_encode([
                'banner_url' => $bannerUrl,
                'thumb_url'  => $thumbUrl,
                'game_url'   => $gameUrl . '/' . $gameId,
            ]));

            $meta = json_decode($gameData['meta'], true) ?? [];

            // update the flags
            $flags = 0;
            if ($gameData['global'] == 1) {
                $flags += 1;
            }

            if ($gameData['coming_soon'] == 1) {
                $flags += 4;
            }

            if ($meta['unity'] ?? false) {
                $flags += 8;
            }

            if ($meta['desktop'] ?? false) {
                $flags += 16;
            }

            unset($meta['unity']);
            unset($meta['desktop']);

            $updatedMeta = str_replace('"', '\"', json_encode($meta));

            $this->execute(
                'UPDATE games SET ' .
                'uris = "' . $uris . '", ' .
                'flags = ' . $flags . ', ' .
                'meta = "' . $updatedMeta . '"' .
                ' WHERE game_id = "' . $gameId . '"'
            );
        }
    }

    /**
     * @return string
     */
    protected function getMediaUrl()
    {
        // check local config file
        $localConfig = include __DIR__ . '/../../config/autoload/zzzzzz.local.php';
        if (isset($localConfig['media-service']) && isset($localConfig['media-service']['url'])) {
            return $localConfig['media-service']['url'];
        }

        // check global media file
        $globalConfig = include __DIR__ . '/../../config/autoload/media.global.php';
        if (isset($globalConfig['media-service']) && isset($globalConfig['media-service']['url'])) {
            return $globalConfig['media-service']['url'];
        }

        // throw exception to prevent migration
        throw new RuntimeException('Cannot run this migration without knowing where the media server is');
    }

    /**
     * @return string
     */
    protected function getGameUrl()
    {
        // check local config file
        $localConfig = include __DIR__ . '/../../config/autoload/zzzzzz.local.php';
        if (isset($localConfig['game']) && isset($localConfig['game']['url'])) {
            return $localConfig['game']['url'];
        }

        // check global media file
        $globalConfig = include __DIR__ . '/../../config/autoload/game.global.php';
        if (isset($globalConfig['game']) && isset($globalConfig['game']['url'])) {
            return $globalConfig['game']['url'];
        }

        // throw exception to prevent migration
        throw new RuntimeException('Cannot run this migration without knowing where the games server is');
    }
}
