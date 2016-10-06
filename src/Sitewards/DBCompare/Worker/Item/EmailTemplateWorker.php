<?php

namespace Sitewards\DBCompare\Worker\Item;

use Sitewards\DBCompare\Worker\DBWorker;

class EmailTemplateWorker extends AbstractItemWorker
{
    const S_WORKER_ID = 'email_template';

    /**
     * Build an sql string to get the difference between two databases
     *
     * @return string
     */
    protected function getDifferenceSql()
    {
        return sprintf(
            'SELECT
                new_email.template_id,
                new_email.template_code,
                new_email.template_text,
                new_email.template_styles,
                new_email.template_type,
                new_email.template_subject,
                new_email.template_sender_name,
                new_email.template_sender_email,
                new_email.added_at,
                new_email.modified_at,
                new_email.orig_template_code,
                new_email.orig_template_variables
            FROM
                %s.core_email_template AS new_email
            WHERE
                ROW(
                    new_email.template_id,
                    new_email.template_code,
                    new_email.template_text,
                    new_email.template_styles,
                    new_email.template_type,
                    new_email.template_subject,
                    new_email.template_sender_name,
                    new_email.template_sender_email
                ) NOT IN (
                    SELECT
                        old_email.template_id,
                        old_email.template_code,
                        old_email.template_text,
                        old_email.template_styles,
                        old_email.template_type,
                        old_email.template_subject,
                        old_email.template_sender_name,
                        old_email.template_sender_email
                    FROM
                        %s.core_email_template AS old_email
                )',
            DBWorker::S_MERGE_DB_NAME,
            DBWorker::S_MAIN_DB_NAME
        );
    }

    /**
     * Write an insert into script for a given difference
     *
     * @param array $aRowData
     */
    protected function writeDifferenceToFile(array $aRowData)
    {
        file_put_contents(
            $this->getDiffFileName(),
            sprintf(
                "INSERT INTO core_email_template (template_id, template_code, template_text, template_styles, template_type, template_subject, template_sender_name, template_sender_email, added_at, modified_at, orig_template_code, orig_template_variables) VALUE (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE template_text=VALUE(template_text), template_styles=VALUE(template_styles), template_type=VALUE(template_type), template_subject=VALUE(template_subject), template_sender_name=VALUE(template_sender_name), template_sender_email=VALUE(template_sender_email);\n",
                $this->getConnection()->quote($aRowData['template_id'], \PDO::PARAM_INT),
                $this->getConnection()->quote($aRowData['template_code'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['template_text'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['template_styles'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['template_type'], \PDO::PARAM_INT),
                $this->getConnection()->quote($aRowData['template_subject'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['template_sender_name'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['template_sender_email'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['added_at'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['modified_at'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['orig_template_code'], \PDO::PARAM_STR),
                $this->getConnection()->quote($aRowData['orig_template_variables'], \PDO::PARAM_STR)
            ),
            FILE_APPEND | LOCK_EX
        );
    }
}