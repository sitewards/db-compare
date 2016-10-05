# Sitewards: db-compare

The aim of this tool is to build a comparison tool between two databases and provide a sql file containing the differences. The main thought here is to allow an admin user to create pages in the Magento and migrate them to live after creatation.

## Takes

1. Main database file,
2. Merging database file,

## Asks

1. Temporary DB information (user, password, db),
2. What models to merge (cms pages, cms blocks, system config...),

## Gives

1. SQL file that can be run on the main database to add new and update existing entries,
2. File containing a list of files that need to be migrated to the main server,

## Needs

1. File system,
2. DB connector,
