<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/1/2016
 * Time: 10:17 PM
 */

namespace Mms\Organizations\Command;

use App\Models\Year;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mms\Laravel\Eloquent\ModelManager;
use Mms\Organizations\OrganizationManager;

class BuildOrganizationsHierarchyCommand extends Command
{
    /**
     * @var ModelManager
     */
    private $manager;
    /**
     * @var OrganizationManager
     */
    private $organizationManager;
    public function __construct(ModelManager $manager, OrganizationManager $organizationManager)
    {
        parent::__construct();
        $this->manager             = $manager;
        $this->organizationManager = $organizationManager;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mms:organizations:hierarchy {year} {type?} {--opt1=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds organizations hierarchy';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $models = $this->organizationManager->getConfiguration();
        $year = $this->manager->getModelRepository(Year::class)->findByCode($this->input->getArgument('year'));
        $arguments = $this->input->getArgument('type');
        if(!$arguments || $arguments == '_') {
            $codes = array_keys($models);
        } else {
            $codes = array_map('trim', explode(',', $arguments));
        }
        foreach ($codes as $code) {
            $modelClass = $this->organizationManager->getModelClass($code);
            $limit = $this->input->getOption('limit') ?: 0;
            $count = 1;
            foreach ($this->manager->getModelRepository($modelClass)->findAll() as $instance) {
                //Put each tree in a transaction
                DB::beginTransaction();
                try {
                    $organization = $this->organizationManager->findOrganization($year, $instance);
                    if (!$organization) {
                        throw new \RuntimeException(sprintf(
                            "No organization found for the year '%s' and the model '%s'",
                            $year, $instance
                        ));
                    }
                    $rootNode = $this->organizationManager->findRoot($organization);
                    if ($rootNode) {
                        $this->warn("Hierarchy for '$organization' already exists'");
                    } else {
                        $this->info("Building hierarchy for '$organization'");
                        $rootNode = $this->organizationManager->buildHierarchy($organization);
                        if($limit && ($count++ >= $limit))
                        {
                            break;
                        }
                        //break;
                    }
                }  catch (\Exception $exception) {
                    DB::rollBack();
                    throw $exception;
                }
                DB::commit();
            }
        }
    }
}