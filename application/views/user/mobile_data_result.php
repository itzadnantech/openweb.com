<div class="lead">
    <?php

    /*
            array(2) { ["result"]=> bool(true) ["message"]=> string(14) "Success fields" }
            array(2) { ["result"]=> bool(false) ["message"]=> string(57) "The filetype you are attempting to upload is not allowed." }
            array(2) { ["result"]=> bool(true) ["message"]=> string(15) "Success message" }
*/


        // Thank you. We will verify your documents and get back to you shortly.

        var_dump($fields_result);
        echo "<br/>";

        var_dump($proof_result);
        echo "<br/>";

        var_dump($passport_result);
        echo "<br/>";

        var_dump($back_link);
        echo "<br/>";

    ?>
</div>