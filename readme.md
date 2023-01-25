# TripIQ Interview Test

The objective of this test is to see how you approach a hard problem. This is not a brain-teaser per-se; this is actually based on a component we use in production.

:exclamation: **This test does not have nor immediately require an interface (nor is one expected for submission); all interaction is performed via the command line.**

Install PHP 7 (7.0 or later will do just fine) and composer and run `composer install` in the project root.

There should be no need to setup [Homestead](https://laravel.com/docs/5.4/homestead) or [Valet](https://laravel.com/docs/5.4/valet)
environments, but if it helps your development then please feel free.

If you have any questions or problems regarding this test, please get in touch with
`kieran@tripiq.eu`, or your relevant recruitment contact.

## Introduction

TripIQ develops a car-sharing PaaS for car-club operators which allows users to book vehicles.

When a booking (or _trip_) is completed, the start time, end time and distance are all used to calculate
the total cost based on a set of pre-defined rates.

These rates can have constraints that only allow them to apply during certain times of the day, for
certain durations, or for increasing distances (for example, the first x miles are free).

Using the below scenarios, we would like to see your implementation for a calculator class that
can take a set of _trip data_ inputs and return a total cost for the duration and distance travelled.

Feel free to use any of the classes already provided or to create your own where you deem necessary.

-----

## Assumptions

* All monetary values should be represented as **integers** in the lowest denomination of the given currency.
  * i.e. £1 would be represented as `100` (100p), £10 as `1000` (no delimiters required).
* Currency should be specified externally, i.e. you are only working with integers.
* Rates are final and immutable once created and persist in a database.
* You do not have to deal with tax whatsoever; it is dealt with after-the-fact during billing.
* The minimum billing period is **15 minutes**. There is a **5 minute** offset, however.
  * This means that if a trip _starts_ at 13:04, we bill from 13:00. If it _ends_ at 13:09, we bill until 13:15.
  * With the offset, if a trip _ends_ at 13:17, we will only bill until 13:15.
  * There is a [helper `clamp`](app/helpers.php#L16) function provided that can be used to perform this rounding.

## Scenarios

There are three different scenarios within this task - two **required** (A and B) and one **optional** (C).

A default [`Calculator`](src/Rates/Calculator.php) class has been created for you.
You are free to use and modify this as you see fit, or delete it entirely.

You may create a new `Calculator` class for each Scenario, or create a single one that covers all.
Regardless, ensure you update [`CalculatorTest::getCalculator`](test/CalculatorTest.php#L21) to return the correct `Calculator` instance for each scenario.

You shouldn't need to touch the rest of the `CalculatorTest` class (unless you are enabling scenario C tests).

Every `Rate` supplied to the `Calculator` will in reality be supplied from a database.
There will be multiple `Rate` objects passed in; you can encapsulate these within another object if you prefer.

-----

### Scenario A

In this scenario, we're testing a set of rates in use by Client A.

They like simplicity, and their rates are:

 - £4.00 per hour
 
 They also bill 15p per mile, but none of the test cases in Scenario A have mileage on them.

### Scenario B

Client B has heard about the success of Client A's calculations and wants to launch with a similar set of rates.

Their rates are as follows:

 - €15 per hour
 - €85 maximum per 24 hour period booked

In addition, they also charge 50c per kilometre, but the first 50 are free.


### Scenario C (Optional)

**This Scenario is not required, but thoroughly recommended**.

Client C has been in the car-sharing industry for decades, and they are migrating from an older system.

During their years, their rates have become more and more complex. They would like to ensure that these rates are kept across platforms. These are based off of actual requests we have had from clients.

Their rates are:

 - £2.00 an hour during weekends
 - £6.65 an hour between 7am and 7pm on weekdays
 - £4.00 an hour outside of 7am to 7pm on weekdays
 - £12.00 max between 9pm and 6am on weekdays
 - £39.00 max per day

They charge 1p per mile for the first 100 miles, then 20p thereafter.

_If you find Scenario C too difficult, you may choose to do a subset of these requirements, but ensure you update the test suite accordingly._

-----

## Testing

We have provided a test class to determine trip costs are calculated correctly.

Tests should be run using `vendor/bin/phpunit` from your root directory.

If you like, you can add several more test cases for your `Calculator` class (and any other classes you may add).

All tests for at least scenarios A and B must pass before you submit your solution.

## Restrictions

- You are free to use anything already included by `laravel/lumen-framework`.
- If you need to add another package, explain why it was needed, but dont feel like you can't use anything else.
- When rounding values, the default `round` is fine.

## Submission

Once you are satisfied with your solution, create a `submission.md` file in the root directory of your
repository. Use this to justify any additional packages you may have required in `composer.json` and
to provide any additional instructions for getting your solution running (database instructions, etc).

Finally, e-mail a link to your repository back to `kieran@tripiq.eu` or to your recruitment contact.
