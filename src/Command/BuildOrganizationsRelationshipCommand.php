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
use Mms\Organizations\Eloquent\YearInterface;
use Mms\Organizations\OrganizationManager;
use Mms\Organizations\Tree\ArrayGenerator;
use Mms\Organizations\Tree\Generator\EditorGenerator;
use Mms\Organizations\Tree\NestedSet\Generator;
use Mms\Organizations\Tree\ToArrayVisitor;

class BuildOrganizationsRelationshipCommand extends Command
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
    protected $signature = 'mms:organizations:relationship {year} {type?} {--opt1=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds organizations relationship';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        //Illuminate\Database\DatabaseManager
        //pyk_die(get_class($this->laravel['db']));
        $models = $this->organizationManager->getConfiguration();
        $yearArgument = $this->input->getArgument('year');
        $yearRecords = [];
        if($yearArgument and !in_array($yearArgument, ['_'])) {
            foreach (explode(',', $yearArgument) as $yearCode) {
                $yearRecords[] = $this->manager->getModelRepository(Year::class)
                    ->findByCode($yearCode);
            }
        } else {
            $yearRecords = $this->manager->getModelRepository(Year::class)->findAll();
        }
        foreach ($yearRecords as $year) {
            /**
             * @var YearInterface $year
             */
            $arguments = $this->input->getArgument('type');
            if (!$arguments || $arguments == '_') {
                $codes = array_keys($models);
            } else {
                $codes = array_map('trim', explode(',', $arguments));
            }
            $nestedSetVisitor = new ToArrayVisitor(new Generator());
            $hierarchyVisitor = new ToArrayVisitor(new EditorGenerator());
            foreach ($codes as $code) {
                $this->info("Building relationships for the year $year and $code references");
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
                            $this->info("Building relationship for '$organization'");
                            $rootNode = $this->organizationManager->buildRelationshipInitializerTree($organization);
                            $this->organizationManager->persistRelationshipTree(
                                $rootNode,
                                $this->laravel['db']->connection()
                            );
                            if ($limit && ($count++ >= $limit)) {
                                DB::commit();
                                break;
                            }
                        }
                    } catch (\Exception $exception) {
                        DB::rollBack();
                        throw $exception;
                    }
                    DB::commit();
                    //break;
                }
            }
        }
    }
}