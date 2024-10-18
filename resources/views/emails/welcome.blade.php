<DOCTYPE html>
    <html lang="en-US">

    <head>
        <meta charset="utf-8">
    </head>

    <body>
        <h3> Hi {{ $data['name'] }}, welcome to Cipree World! <br>
            </h3>
           <h3>  Please login to your new account with below details by clicking on the link provided. </h3>
            <h3>Email: {{ $data['email'] }} </h3>
            <h3>Password: {{ $data['password'] }}</h3> <br>

            <p><a href="https://test.cipree.com/login">Login Page</a></p>
            <p id="p1"><a href="{{ route('password.request') }}" class="ml-auto">Note: You may be required to change your password after you first login. Click here.</a> </p>
            <p>We wish you the best of Cipree experience</p>

             <p>- Cipree Team</p>

    </body>

    </html>
