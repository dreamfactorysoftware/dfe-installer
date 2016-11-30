<?php

use Illuminate\Database\Seeder;

class AppKeyTTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

            \DB::table('app_key_t')->delete();

            \DB::table('app_key_t')->insert(array(
                0 =>
                    array(
                        'id' => 1,
                        'owner_id' => 1,
                        'owner_type_nbr' => 5,
                        'client_id' => '08ee9de424d3b6543ec5e8c47646af9d7f6e9a8c101a7d2cad83ac75bb3167c7',
                        'client_secret' => 'd4fe9d96ed4ab27c1bcc370c00e9a9bb847366cbfdd183cc52ae75c68436b91c',
                        'server_secret' => 'NWRkMTJmZjQ0YTFkMjQxOGJkOTQ0YjIwNjJjNmRmN2VkZTU2NjA5MTNmOWYxYjJlZjk5YTJmYTI1NjNjNzY4Zg',
                        'key_class_text' => '[entity:service-user]',
                        'created_at' => '2016-07-08 14:05:48',
                        'updated_at' => '2016-07-08 14:05:48',
                    ),
                1 =>
                    array(
                        'id' => 2,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1000,
                        'client_id' => '7fdcdd4b452d5d6883f18b98d761f46039bd891e1d7316a866b1ddf6e755eb5b',
                        'client_secret' => 'e3c55acb6d95706805daf96f302f2450fd5fa527796d25ae6ac7677c0ece7b1e',
                        'server_secret' => 'NWRkMTJmZjQ0YTFkMjQxOGJkOTQ0YjIwNjJjNmRmN2VkZTU2NjA5MTNmOWYxYjJlZjk5YTJmYTI1NjNjNzY4Zg',
                        'key_class_text' => '[entity:console]',
                        'created_at' => '2016-07-08 14:05:48',
                        'updated_at' => '2016-07-08 14:05:48',
                    ),
                2 =>
                    array(
                        'id' => 3,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1001,
                        'client_id' => '74399a009410237ae552efcb92b64334c265a9d85787c052217ddfe5882b6e99',
                        'client_secret' => '99255beabdaa751cd61724fba1fb27626ac56a56b0bfbab34131f14282f25598',
                        'server_secret' => 'NWRkMTJmZjQ0YTFkMjQxOGJkOTQ0YjIwNjJjNmRmN2VkZTU2NjA5MTNmOWYxYjJlZjk5YTJmYTI1NjNjNzY4Zg',
                        'key_class_text' => '[entity:dashboard]',
                        'created_at' => '2016-07-08 14:05:48',
                        'updated_at' => '2016-07-08 14:05:48',
                    ),
                3 =>
                    array(
                        'id' => 4,
                        'owner_id' => 2,
                        'owner_type_nbr' => 5,
                        'client_id' => '0a3324cd0fb70e57d732a5a4801f786cf8d902c34d7c9dd2cedd932eed7c2662',
                        'client_secret' => '3a492e3e99e1e7dc1f539b150035a8b3ea02ec8a40e3a0c2ca7b6266308f0760',
                        'server_secret' => 'NGZmNzBmYTU5NDdkYmQwMDdhNmY2NjhkMTU0MzVmZTk5MzY4YmU2MGVlMWI1M2I1NTE2YjY1MTljOTg0OTQyMw',
                        'key_class_text' => '[entity:service-user]',
                        'created_at' => '2016-07-15 16:55:33',
                        'updated_at' => '2016-07-15 16:55:33',
                    ),
                4 =>
                    array(
                        'id' => 5,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1000,
                        'client_id' => 'cd9066bd4b1546d38de55c90ea14ebdda53ff2ea13aa7ba4a89d09356c16adf2',
                        'client_secret' => 'f34fdfa0c55b3dd127adc7d2d5f4369ce09a19339afe49402d3621f9ceb9799f',
                        'server_secret' => 'NGZmNzBmYTU5NDdkYmQwMDdhNmY2NjhkMTU0MzVmZTk5MzY4YmU2MGVlMWI1M2I1NTE2YjY1MTljOTg0OTQyMw',
                        'key_class_text' => '[entity:console]',
                        'created_at' => '2016-07-15 16:55:33',
                        'updated_at' => '2016-07-15 16:55:33',
                    ),
                5 =>
                    array(
                        'id' => 6,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1001,
                        'client_id' => 'dab354a90f16409d2686ff4c93b0594144865720c8ea16eb886b34c42e38c073',
                        'client_secret' => 'eca593c3e04145880569e773fef42d499f8a00821cdcb4d8ab4b40c359f964d7',
                        'server_secret' => 'NGZmNzBmYTU5NDdkYmQwMDdhNmY2NjhkMTU0MzVmZTk5MzY4YmU2MGVlMWI1M2I1NTE2YjY1MTljOTg0OTQyMw',
                        'key_class_text' => '[entity:dashboard]',
                        'created_at' => '2016-07-15 16:55:33',
                        'updated_at' => '2016-07-15 16:55:33',
                    ),
                6 =>
                    array(
                        'id' => 7,
                        'owner_id' => 3,
                        'owner_type_nbr' => 5,
                        'client_id' => 'dc8573570d69cfee1372253db646aa6d698b797b84365cb34cdfdd3699ee00eb',
                        'client_secret' => 'ba0b8845f052da68c2aa18a69497bce9701be339ddf185f313c9d4c2d48b8043',
                        'server_secret' => 'NjhhMTZkYjU1MTI0MTdhODIxYjJkYmE2ZjMzNzQ0Mzg0YWRmNzlkMGRjMDMyNjdjYTgyZjYwYzFiM2Y1YmNkYQ',
                        'key_class_text' => '[entity:service-user]',
                        'created_at' => '2016-07-15 18:22:10',
                        'updated_at' => '2016-07-15 18:22:10',
                    ),
                7 =>
                    array(
                        'id' => 8,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1000,
                        'client_id' => '44e0a3765de68289fe797e5b8a7fdaa285e79df0a1023513d37b8e8a0ac0ef4d',
                        'client_secret' => 'b55e4b04cdc89512f0bb5cd1872ea1fc58f0f7818a6c0d87abfb4803b48ccbae',
                        'server_secret' => 'NjhhMTZkYjU1MTI0MTdhODIxYjJkYmE2ZjMzNzQ0Mzg0YWRmNzlkMGRjMDMyNjdjYTgyZjYwYzFiM2Y1YmNkYQ',
                        'key_class_text' => '[entity:console]',
                        'created_at' => '2016-07-15 18:22:10',
                        'updated_at' => '2016-07-15 18:22:10',
                    ),
                8 =>
                    array(
                        'id' => 9,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1001,
                        'client_id' => 'b967088f2b6f8f9dd1fdaadd45ededcefc40fe722008d819e992642e0f2f9998',
                        'client_secret' => '94f898e661114ee40598ed800e41f01179773b98d9532aba11a178d838aee2e1',
                        'server_secret' => 'NjhhMTZkYjU1MTI0MTdhODIxYjJkYmE2ZjMzNzQ0Mzg0YWRmNzlkMGRjMDMyNjdjYTgyZjYwYzFiM2Y1YmNkYQ',
                        'key_class_text' => '[entity:dashboard]',
                        'created_at' => '2016-07-15 18:22:10',
                        'updated_at' => '2016-07-15 18:22:10',
                    ),
                9 =>
                    array(
                        'id' => 10,
                        'owner_id' => 4,
                        'owner_type_nbr' => 5,
                        'client_id' => '147c56aab316e9ff73cfe00dcf8e979dfd1f58ff97476ffb29da83ac9c56445a',
                        'client_secret' => '5b87a81db00341649a391b2dd383c37fa9665637955a241961d98100a5d07557',
                        'server_secret' => 'MzlkZTIwOGMxZTczMjI4OTc0NDJmYzJlNzU2Mjk5OGFhYWZiMWQ3OTQyZTcwZTM5Y2E2Y2Y3MDkxZWEwZWRiMQ',
                        'key_class_text' => '[entity:service-user]',
                        'created_at' => '2016-07-15 18:27:27',
                        'updated_at' => '2016-07-15 18:27:27',
                    ),
                10 =>
                    array(
                        'id' => 11,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1000,
                        'client_id' => '844d46e9e7866de8b0f5cc9e32d3c6394b3ce0dd3ab0dc110afde5bb2019037b',
                        'client_secret' => '124d59268b8e69f9681bfeb48298f82cbd46694593f8f0ae7d0566c5ab2ebba4',
                        'server_secret' => 'MzlkZTIwOGMxZTczMjI4OTc0NDJmYzJlNzU2Mjk5OGFhYWZiMWQ3OTQyZTcwZTM5Y2E2Y2Y3MDkxZWEwZWRiMQ',
                        'key_class_text' => '[entity:console]',
                        'created_at' => '2016-07-15 18:27:27',
                        'updated_at' => '2016-07-15 18:27:27',
                    ),
                11 =>
                    array(
                        'id' => 12,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1001,
                        'client_id' => 'd35aa81967506b0dd5be19b8960cfc65e3ca5e3f43d5f00366a6f9d7584ecbb6',
                        'client_secret' => '9ceaa965728f69e46e2c4a35b0afd622f6809bd0fe7f8c0e5e50b8a227c9a0cd',
                        'server_secret' => 'MzlkZTIwOGMxZTczMjI4OTc0NDJmYzJlNzU2Mjk5OGFhYWZiMWQ3OTQyZTcwZTM5Y2E2Y2Y3MDkxZWEwZWRiMQ',
                        'key_class_text' => '[entity:dashboard]',
                        'created_at' => '2016-07-15 18:27:27',
                        'updated_at' => '2016-07-15 18:27:27',
                    ),
                12 =>
                    array(
                        'id' => 13,
                        'owner_id' => 5,
                        'owner_type_nbr' => 5,
                        'client_id' => 'b8a12beb13877488abce3942bcc4d678b291862cd597cdb2715576baa13e2fa7',
                        'client_secret' => 'eb9ae0e6b278409d2968e97f5503e5afaf003a18efa754d4d9a4a4a0a85d076f',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:service-user]',
                        'created_at' => '2016-07-15 18:32:32',
                        'updated_at' => '2016-07-15 18:32:32',
                    ),
                13 =>
                    array(
                        'id' => 14,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1000,
                        'client_id' => '1ce016cfb9903a958f7b805892c1f5b25d438a44c3f72e9391572eae82dd9ae6',
                        'client_secret' => 'ef694e9aa84573448ca4c2a17d9b7dfb265f2017b018978e47dc7b75f21a0271',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:console]',
                        'created_at' => '2016-07-15 18:32:32',
                        'updated_at' => '2016-07-15 18:32:32',
                    ),
                14 =>
                    array(
                        'id' => 15,
                        'owner_id' => 0,
                        'owner_type_nbr' => 1001,
                        'client_id' => '28b80dc6a63c02a99a3ce48297ed6c079e91505f38089d5db593338053a9ddbe',
                        'client_secret' => '62a8ffb6f154513e8934f70d81f4d847a41d7676685d29c49a1f2d236bf4bff5',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:dashboard]',
                        'created_at' => '2016-07-15 18:32:32',
                        'updated_at' => '2016-07-15 18:32:32',
                    ),
                15 =>
                    array(
                        'id' => 16,
                        'owner_id' => 1,
                        'owner_type_nbr' => 0,
                        'client_id' => 'b4baa509d2ea5519ae6d0b2313ccb33331fb4de06139d05973f0be81c15b7d41',
                        'client_secret' => '5f29802444fde08972919719cb7f357e021f9b72fe7ee27cb1912be3ca4011ea',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:user]',
                        'created_at' => '2016-07-15 19:26:14',
                        'updated_at' => '2016-07-15 19:26:14',
                    ),
                16 =>
                    array(
                        'id' => 17,
                        'owner_id' => 1,
                        'owner_type_nbr' => 1,
                        'client_id' => '4fc60f9e3ea22c5b22577c1ef8ef8fe663fde0b36ac75adf62be72b87fddb2e9',
                        'client_secret' => '11471a8ce21181b0276447ab84a9979c34fe5102e6fb1998d87b9d973b253ecf',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-15 19:26:29',
                        'updated_at' => '2016-07-15 19:26:29',
                    ),
                17 =>
                    array(
                        'id' => 18,
                        'owner_id' => 1,
                        'owner_type_nbr' => 1,
                        'client_id' => '2be6f609a753351977a7ef443bb9fa66972f6cb765f04ddd157b88e32dfc418c',
                        'client_secret' => '1094b89d412e4a3148ef9c8cdb538952da72aa0bfb10e436d64412fdcf7c02ef',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-15 19:26:29',
                        'updated_at' => '2016-07-15 19:26:29',
                    ),
                18 =>
                    array(
                        'id' => 19,
                        'owner_id' => 6,
                        'owner_type_nbr' => 5,
                        'client_id' => '435f1faa303d5c14795e2391a9cbe79ea64db243cab2f5191b6bed8e6b97a3ca',
                        'client_secret' => '4c42cc18e03b28effb12ef606fb6410fc3c8529ffae7368afe92a12741632f58',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:service-user]',
                        'created_at' => '2016-07-19 10:21:13',
                        'updated_at' => '2016-07-19 10:21:13',
                    ),
                19 =>
                    array(
                        'id' => 20,
                        'owner_id' => 2,
                        'owner_type_nbr' => 1,
                        'client_id' => '1858495a636ea31c7254f935ce74bd1dd613cfc02f1b11541456a582d2827189',
                        'client_secret' => '7edc853fe96bec4afc7e5a8d6c411c61bb887eaa34bd34da881d0dc12ef90ee3',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 10:24:02',
                        'updated_at' => '2016-07-19 10:24:02',
                    ),
                20 =>
                    array(
                        'id' => 21,
                        'owner_id' => 2,
                        'owner_type_nbr' => 1,
                        'client_id' => '1bdf21afdbcec40f38abe9275934adf0fcf801e02cd267a8b837e83d73b86573',
                        'client_secret' => '7035aa771dd6da708b04f1e62f7766aa920ba725e00f5a0a8781b2ecbe55cc08',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 10:24:02',
                        'updated_at' => '2016-07-19 10:24:02',
                    ),
                21 =>
                    array(
                        'id' => 22,
                        'owner_id' => 3,
                        'owner_type_nbr' => 1,
                        'client_id' => '905cd81da7b74ec91ba9119395d30c9dc6ac4654c78c6cb4e654cbaab74f3249',
                        'client_secret' => 'f5c546f537516fea6b55a97728ca2c38a0ccc22e1ffb60efa7b7840f06a1575a',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:28:03',
                        'updated_at' => '2016-07-19 14:28:03',
                    ),
                22 =>
                    array(
                        'id' => 23,
                        'owner_id' => 3,
                        'owner_type_nbr' => 1,
                        'client_id' => '68ece310e99873ea8ecd4f08ce363e699999f7bba10c75a9de6cb32649646bfb',
                        'client_secret' => 'b4902ac8eae4eda84d680d968a08ac5c6c5a42c906612824d0a0930374a5f1bf',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:28:03',
                        'updated_at' => '2016-07-19 14:28:03',
                    ),
                23 =>
                    array(
                        'id' => 24,
                        'owner_id' => 4,
                        'owner_type_nbr' => 1,
                        'client_id' => '5c5ef6de1bcf827c00eacb75540ea669a2a9b9e4c4452abb49e114997b5dcac2',
                        'client_secret' => 'a994b0380d2ac9b687cbcc0390c3daa6d091125af014fe1df6da01b67f9f12be',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:30:03',
                        'updated_at' => '2016-07-19 14:30:03',
                    ),
                24 =>
                    array(
                        'id' => 25,
                        'owner_id' => 4,
                        'owner_type_nbr' => 1,
                        'client_id' => '7f65e3d9f76f1a553a0f915e60ac34b668bc89d5816fcc233257f498eb53d9ba',
                        'client_secret' => 'c1ed19e53213e76c14fe10eb52845eef2206cbd37b14160984f0d3bd3abc91e8',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:30:03',
                        'updated_at' => '2016-07-19 14:30:03',
                    ),
                25 =>
                    array(
                        'id' => 26,
                        'owner_id' => 5,
                        'owner_type_nbr' => 1,
                        'client_id' => '67d184c830e5dcbfceb68e1ee0f749639e7117a3c2ec5b1a6f75ee62cdd016ee',
                        'client_secret' => 'c0cea6b14b6c9b097c2297ec9a99d7dba7f0caf82e4d8e6c821edf7c9d23078e',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:35:15',
                        'updated_at' => '2016-07-19 14:35:15',
                    ),
                26 =>
                    array(
                        'id' => 27,
                        'owner_id' => 5,
                        'owner_type_nbr' => 1,
                        'client_id' => 'f7684416c80ed2b6d825f15b05fbc55a16dc5734b5f784cb78db5e0d4c40e4b3',
                        'client_secret' => 'cd815280d90d537c8bba7fa0c620f61577169a34d6548dec41e9724c79c53faa',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:35:15',
                        'updated_at' => '2016-07-19 14:35:15',
                    ),
                27 =>
                    array(
                        'id' => 30,
                        'owner_id' => 7,
                        'owner_type_nbr' => 1,
                        'client_id' => '08340580a1a324842b0376fb41280d216a06a19c617b809ab914ec6a5428aefb',
                        'client_secret' => '40587cb4088414815745760154587247b4d38342df775364eb91bd9a297c9808',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:38:26',
                        'updated_at' => '2016-07-19 14:38:26',
                    ),
                28 =>
                    array(
                        'id' => 31,
                        'owner_id' => 7,
                        'owner_type_nbr' => 1,
                        'client_id' => 'e6d9edf8fe7ff8d461711449149a12c83e1d3815c67eb989caad9c99a3175e0f',
                        'client_secret' => '2b32bc7b6c59fba164a9465f3734d329eeb3a483674e09b2be6c05b1772674a1',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:38:26',
                        'updated_at' => '2016-07-19 14:38:26',
                    ),
                29 =>
                    array(
                        'id' => 32,
                        'owner_id' => 8,
                        'owner_type_nbr' => 1,
                        'client_id' => '0acd25f102a59b7446a73a5c6e03d2bfd56ef9390e3fdd07413c0271dd4a0395',
                        'client_secret' => '8c0f8935499384b2b799258d0e53db4bb0f1e58cf7559a0416e40d2217c98ceb',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:41:31',
                        'updated_at' => '2016-07-19 14:41:31',
                    ),
                30 =>
                    array(
                        'id' => 33,
                        'owner_id' => 8,
                        'owner_type_nbr' => 1,
                        'client_id' => 'c4b7ff456610520892aab2d6141549db26058c59e81fd5ec1e7e3456027cfa51',
                        'client_secret' => '37766a500ea961b18c33924294719bc3fac6a04699fa76ac5983812b935c5eb6',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 14:41:31',
                        'updated_at' => '2016-07-19 14:41:31',
                    ),
                31 =>
                    array(
                        'id' => 34,
                        'owner_id' => 9,
                        'owner_type_nbr' => 1,
                        'client_id' => 'deca18707b9cb1b96f528972253ae3a00682aebb03c14eceebbbeb614a2724ca',
                        'client_secret' => '5de9495f66a70eac847d7a285a7ad09dcbad60add0f6e216f6db6b78f4d0d06e',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 15:49:49',
                        'updated_at' => '2016-07-19 15:49:49',
                    ),
                32 =>
                    array(
                        'id' => 35,
                        'owner_id' => 9,
                        'owner_type_nbr' => 1,
                        'client_id' => '7ad943918bed86176eecfda7b2304d339a1c85f12719b752882e3c52c202d224',
                        'client_secret' => '28c53b35e0e99eb936ea580c1951be639997b2fdf670e7626c594c17d6cb87fe',
                        'server_secret' => 'MTkzYWVkMDFjMmI3YjkxY2QyY2FkMjNhYmY0ODMwODY5ZjY3NzRkYTE0NWJmYmJlNDllMGJjZjczYWNjODIyYw',
                        'key_class_text' => '[entity:instance]',
                        'created_at' => '2016-07-19 15:49:49',
                        'updated_at' => '2016-07-19 15:49:49',
                    ),
            ));
        }
        
        
}
