### Refactorings
  
 * GOAL 1, Split Manager into two where: Manager extends ManagerBasic
 * GOAL 2, ManagerBasic to take a simpler Environment rather than a Config
 * GOAL 3, ManagerBasic to have no dependency on Symphony Console
 
 1. Manager extends ManagerBasic
 2. _construct() less particular (types are still defined on setInput() and setOutput() 
 3. added $this->verbose flag
 4. tidy uses of getVerbosity() (lower dependency on OutputInterface)
 5. BasicManager writeln() - allows $this to substitute for $output  
 6. getVersionOrder() accessor instead of getConfig()->getVersionOrder()
 7. isVersionOrderCreationTime() accessor instead of getConfig()->isVersionOrderCreationTime()
 8. getSeedPaths() accessor instead of getConfig()->getSeedPaths()
 9.  getMigrationPaths() accessor instead of getConfig()->getMigrationPaths()
 10. configHasEnvironment($name) accessor instead of getConfig()->hasEnvironment($name)
 11. configGetEnvironment($name) accessor instead of getConfig()->getEnvironment($name)
 12. add NamespaceAwareInterface and NamespaceAwareTrait to ManagerBasic
 13. remove last direct reference to getConfig()
 14. promote most functions from Manager to ManagerBasic
 15. copy over const VERSION_ORDER_CREATION_TIME: from Config to ManagerBasic
 16. copy over const VERSION_ORDER_EXECUTION_TIME: from Config to ManagerBasic
 17. update uses of const to refer to local definitions
 18. DONT: update PDOAdapter->getVersionLog() to use ManagerBasic::VERSION_ORDER_CREATION_TIME
  or  ManagerBasic::VERSION_ORDER_EXECUTION_TIME unless PDOAdapter depends on Manager
 19. Implemented getOptions() to get the environment values/settings array
 20. Implemented generic getPaths( $seedsOrMigrations )
 21. getSeedsPaths() returns getPaths('seeds')
 22. getMigrationsPaths() returns getPaths('migrations')
  