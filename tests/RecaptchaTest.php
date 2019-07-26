<?php

use App\API\V1\UnitTest\CrudTestBase;
use Faker\Factory;

class RecaptchaTest extends CrudTestBase
{
    protected $password = 'Password00!';

    /**
     * @group recaptcha
     * @throws Exception
     */
    public function testRecaptchaFail():void
    {
        //$this->refreshApplication();
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $generator = Factory::create();
            $data = [
                "params" => [
                    'email' => $generator->safeEmail,
                    'firstName'=> $generator->firstName,
                    'middleInitial'=>'X',
                    'lastName'=> $generator->lastName,
                    'age' => $generator->randomNumber(2),
                    'gender' => 1,
                    'weight' => 210,
                    'height' => 180.34,
                    'phoneNumber' => "+1 757-571-2711",
                    'lifestyle' => 1,
                    'password' => $this->password,
                    'job' => $generator->jobTitle,
                    'address' => $generator->address,
                    'locale' => "en"
                ]
            ];

            $response = $this->json('POST', '/contexts/guest/users', $data);
            /** tempesttools_recaptcha_no_parameters **/
            $response->assertResponseStatus(400);

            $data["options"] = ["email" => false];
            $response = $this->json('POST', '/contexts/guest/users', $data);
            /** tempesttools_recaptcha_no_value **/
            $response->assertResponseStatus(400);

            $data["options"] = ["email" => false, "g-recaptcha-response" => "03ANcjosru1X31dkG21WqNwzebaheCRaaEv5L7WTsjSUIqa_tYJ_Pyqzuf1NuTqWTAdhFyivpSGBrOljL9GodAZsMuaqtk4xMYqkPfdzZsLugm7bj3J9P_1fNdIs4DRMevcpPW7aFtkG8MAI7sHN2G8_b6qc-A_4pPCfvgPK5e1E4arDvgAAWK79483K0GFGOGBxQg1QMrtxd2biNIZYo20HHFJEwtNnaxxoyK5Fi0QkJ_o1EzoqRWliY7p3nv_4so6BS4ojxCe86-a6STxYDB5HQ222MD6nHrEKRq3vWApEZ4oE9iD8-zvOQtSbgHd3zl6_sHrux655WUjzxzxiTmwvFvLdfzgVIG7TUdjp8lbWq8b6lHQiXIlys7nMwb2EwcTofzECXZuAg4h9h9o86_oj_M-Jbufnt7KajcB6CppMWhSVVS5Ft4x9Y"];
            $response = $this->json('POST', '/contexts/guest/users', $data);
            /** tempesttools_recaptcha_invalid **/
            $response->assertResponseStatus(400);

            $data["options"] = ["email" => false, "g-recaptcha-response-omit" => env('GOOGLE_RECAPTCHA_SKIP_CODE')];
            $response = $this->json('POST', '/contexts/guest/users', $data);
            /** success **/
            $response->assertResponseStatus(201);

            /** Nothing comes next. We cannot spoof recaptcha. **/

            /** Leave no trace of test **/
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}