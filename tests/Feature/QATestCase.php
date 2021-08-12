<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Testing\PendingCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\TestCase;

class QATestCase extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function login()
    {
        return $this->artisan('qanda:interactive')
            ->expectsQuestion("Enter your email address\n If the email doesn't exist it will be created", $this->user->email)
            ->expectsOutput('You logged in successfully');
    }

    /**
     * Specify a table that should be printed when the command runs.
     *
     * @param array $headers
     * @param \Illuminate\Contracts\Support\Arrayable|array $rows
     * @param string $header
     * @param string $footer
     * @param string $tableStyle
     * @return $this
     */
    public function expectsTitledTable($headers, $rows, string $header = '', string $footer = '', $tableStyle = 'default')
    {
        $table = (new Table($output = new BufferedOutput))
            ->setHeaders((array)$headers)
            ->setRows($rows instanceof Arrayable ? $rows->toArray() : $rows)
            ->setStyle($tableStyle);

        if (!empty($header)) {
            $table->setHeaderTitle($header);
        }

        if (!empty($footer)) {
            $table->setFooterTitle($footer);
        }

        $table->render();

        $lines = array_filter(
            explode(PHP_EOL, $output->fetch())
        );

        foreach ($lines as $line) {
            $this->expectsOutput($line);
        }

        return $this;
    }
}
