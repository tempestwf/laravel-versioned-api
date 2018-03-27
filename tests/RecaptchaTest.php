<?php

class RecaptchaTest extends TestCase
{
    public function testRecaptchaFail():void
    {
        $response = $this->json('GET', '/contexts/guest/users',
            [
                "params" => [
                    "name" => "Jerome Erazo",
                    "email" => "monx.erazo@me.com",
                    "password" => "password",
                    "locale" => "en"
                ]
            ]
        );
        /** general_no_parameters **/
        $response->assertResponseStatus(405);

        $response = $this->json('GET', '/contexts/guest/users',
            [
                "params" => [
                    "name" => "Jerome Erazo",
                    "email" => "monx.erazo@me.com",
                    "password" => "password",
                    "locale" => "en"
                ],
                "options" => [
                    "email" => false
                ]
            ]
        );
        /** tempesttools_recaptcha_no_value **/
        $response->assertResponseStatus(405);

        $response = $this->json('GET', '/contexts/guest/users',
            [
                "params" => [
                    "name" => "Jerome Erazo",
                    "email" => "monx.erazo@me.com",
                    "password" => "password",
                    "locale" => "en"
                ],
                "options" => [
                    "email" => false,
                    "g-recaptcha-response" => "03ANcjosru1X31dkG21WqNwzebaheCRaaEv5L7WTsjSUIqa_tYJ_Pyqzuf1NuTqWTAdhFyivpSGBrOljL9GodAZsMuaqtk4xMYqkPfdzZsLugm7bj3J9P_1fNdIs4DRMevcpPW7aFtkG8MAI7sHN2G8_b6qc-A_4pPCfvgPK5e1E4arDvgAAWK79483K0GFGOGBxQg1QMrtxd2biNIZYo20HHFJEwtNnaxxoyK5Fi0QkJ_o1EzoqRWliY7p3nv_4so6BS4ojxCe86-a6STxYDB5HQ222MD6nHrEKRq3vWApEZ4oE9iD8-zvOQtSbgHd3zl6_sHrux655WUjzxzxiTmwvFvLdfzgVIG7TUdjp8lbWq8b6lHQiXIlys7nMwb2EwcTofzECXZuAg4h9h9o86_oj_M-Jbufnt7KajcB6CppMWhSVVS5Ft4x9Y"
                ]
            ]
        );
        /** tempesttools_recaptcha_invalid **/
        $response->assertResponseStatus(405);

        /** Nothing comes next. We cannot spoof recaptcha. **/
    }
}