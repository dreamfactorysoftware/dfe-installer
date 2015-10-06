<?php
use Illuminate\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpKernel\Client;

/**
 * Base class for testing commands
 */
abstract class CommandTestCase extends TestCase
{
    /**
     * Runs a command and returns it output
     *
     * @param \Symfony\Component\HttpKernel\Client $client
     * @param                                      $command
     *
     * @return string|\Symfony\Component\Console\Output\StreamOutput
     */
    public function runCommand( Client $client, $command )
    {
        $application = new Application( $client->getKernel() );
        $application->setAutoExit( false );

        $fp = tmpfile();
        $input = new StringInput( $command );
        $output = new StreamOutput( $fp );

        $application->run( $input, $output );

        fseek( $fp, 0 );
        $output = '';
        while ( !feof( $fp ) )
        {
            $output = fread( $fp, 4096 );
        }
        fclose( $fp );

        return $output;
    }
}