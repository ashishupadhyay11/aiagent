<?php

class ReportsManager extends CComponent
{
    // -------------------------------------------------------------------------------- add new report
    // add to getReportsConfigs map + name translation ("reports.name.key") in fr/vuecomponents and en/vuecomponents
    // add key to getAvailableReports map (permissions)
    // add static function (query_functions) matching getAvailableReports && getReportsConfigs key (name_func) -> need to returns associative array (columns_name: array_keys($assocDataArray['0']), rows: values)

    // -------------------------------------------------------------------------------- navigation
    // configs_functions
    // action_functions
    // query_functions
    // helper_functions

    // -------------------------------------------------------------------------------- configs_functions
    // --------------------- getFields
    // --------------------- getReportsConfigs
    // --------------------- getAvailableReports

    public static function getFields($fields_keys)
    {
        $fields = [
            'iso_week_year' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.filter_iso_week_year'),
                'type' => 'select',
                'value' => Date('Y-W', strtotime('last sunday')),
                'required' => true,
                'initialize' => 'GetIsoWeeksYears',
            ],
            'year_month' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.filter_year_month'),
                'type' => 'select',
                'value' => Date('Y-m', strtotime('last month')),
                'required' => true,
                'initialize' => 'GetYearMonths',
            ],
            'export_type' => [
                'label' => 'Export type',
                'type' => 'toggle',
                'value' => '0',
                'options' => [
                   ['label' => 'Summary', 'value' => '0'],
                   ['label' => 'Raw data', 'value' => '1'],
                ]
            ],
            'subscription_status' => [
                'label' => 'Subscription status',
                'type' => 'select',
                'value' => null,
                'placeholder' => 'All',
                'options' => [
                    'Active',
                    'Inactive',
                ]
            ],
            'subscription_type' => [
                'label' => 'Subscription type',
                'type' => 'select',
                'value' => 'Regular',
                'placeholder' => 'All',
                'options' => [
                    'Trial',
                    'Regular',
                ]
            ],
            'tier_id' => [
                'label' => 'Tier ID',
                'type' => 'select',
                'value' => null,
                'placeholder' => 'All',
                'options' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ]
            ],
            'space' => [
                'type' => 'hidden'
            ],
            'past_start' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.start'),
                'type' => 'date',
                'required' => true,
                'value' => date('Y-m-d', strtotime('-1 week')),
                'max' => date('Y-m-d', strtotime('-1 day')),
            ],
            'past_end' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.end'),
                'type' => 'date',
                'required' => true,
                'value' => date('Y-m-d', strtotime('-1 day')),
                'max' => date('Y-m-d', strtotime('-1 day')),
            ],
            'consider_from' => [
                'label' => Yii::t('vuecomponents', 'Consider orders from'),
                'type' => 'date',
                'required' => false,
                'value' => null,
                'max' => date('Y-m-d', strtotime('-1 day')),
            ],
            'consider_to' => [
                'label' => Yii::t('vuecomponents', 'Consider orders to'),
                'type' => 'date',
                'required' => false,
                'value' => null,
                'max' => date('Y-m-d', strtotime('-1 day')),
            ],
            'signup_start' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.signup_start'),
                'type' => 'date',
                'value' => '2013-01-01',
            ],
            'signup_end' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.signup_end'),
                'type' => 'date',
                'value' => Date('Y-m-d', strtotime('last sunday')),
            ],
            'cancelation_start' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.cancelation_start'),
                'type' => 'date',
                'value' => null,
            ],
            'cancelation_end' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.cancelation_end'),
                'type' => 'date',
                'value' => null,
            ],
            'retention_start' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.retention_start'),
                'type' => 'date',
                'value' => null,
            ],
            'retention_end' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.retention_end'),
                'type' => 'date',
                'value' => null,
            ],
            'start' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.start'),
                'type' => 'date',
                'value' => date('Y-m-d', strtotime('-1 week')),
                'required' => true,
            ],
            'end' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.end'),
                'type' => 'date',
                'value' => date('Y-m-d'),
                'required' => true,
            ],
            'date' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.date'),
                'type' => 'date',
                'value' => date('Y-m-d'),
                'required' => true,
            ],
            'default_group_by' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.default_group_by'),
                'type' => 'select',
                'required' => true,
                'options' => [
                    'Individual Entry',
                    'Day',
                    'Week',
                    'Month',
                ],
            ],
            'employee_ids' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.employee'),
                'type' => 'multiselect',
                'initialize' => 'GetEmployeeList',
            ],
            'company_ids' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.company'),
                'type' => 'multiselect',
                'initialize' => 'GetCompanyList',
            ],
            'weeks' => [
                'label' => 'Weeks',
                'type' => 'number',
                'value' => 1,
                'required' => true,
            ],
            'products' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.product'),
                'label' => 'Products',
                'type' => 'text',
            ],
            'min_locks' => [
                'label' => 'Min Locks',
                'type' => 'number',
                'value' => 1,
                'required' => true,
            ],
            'filter_transactions' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.filter_transactions'),
                'type' => 'select',
                'required' => true,
                'options' => [
                    'All',
                    'Refunds',
                ]
            ],
            'operand' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.filter_operand'),
                'type' => 'select',
                'value' => '',
                'options' => [
                    '=',
                    '>',
                    '>=',
                    '<',
                    '<=',
                ]
            ],
            'working_time' => [
                'label' => Yii::t('vuecomponents', 'reports.fields.filter_working_time'),
                'type' => 'time',
                'value' => '00:00:00',
                'step' => 2,
            ],
            'signup_type' => [
                'label' => 'Has received a trial basket?',
                'type' => 'select',
                'value' => null,
                'options' => [
                    'Yes',
                    'No'
                ]
            ],
            'coupon_code' => [
                'label' => 'Coupon code',
                'type' => 'text',
                'value' => null,
                'placeholder' => 'Filter by used coupon code',
            ],
            'referral_code' => [
                'label' => 'Referral code',
                'type' => 'text',
                'value' => null,
                'placeholder' => 'Filter by sign up referral code',
            ],
            'utm_source' => [
                'label' => 'UTM sign up source',
                'type' => 'text',
                'value' => null,
                'placeholder' => 'Filter by sign up UTM source ',
            ],
            'sign_up_coupon_type' => [
                'label' => 'Sign up coupon type',
                'type' => 'select',
                'value' => null,
                'initialize' => 'GetAllCouponTypes',
                'placeholder' => 'Filter by sign up coupon type',
            ],
            'delivery_address' => [
                'label' => 'Delivery addresses',
                'type' => 'text',
                'value' => null,
                'placeholder' => 'Filter by postal code, city, street',
            ],
            'delivery_type' => [
                'label' => 'Delivery type',
                'type' => 'select',
                'value' => null,
                'placeholder' => 'All',
                'options' => [
                    'HD',
                    'PUP'
                ]
            ],
            'delivery_day' => [
                'label' => 'Main Delivery Day',
                'type' => 'multiselect',
                'value' => null,
                'placeholder' => 'All',
                'options' => [
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday'
                ]
            ],
            'min_take_rate' => [
                'label' => 'Minimum Take Rate (%)',
                'type' => 'number',
                'value' => null,
            ],
            'max_take_rate' => [
                'label' => 'Maximum Take Rate (%)',
                'type' => 'number',
                'value' => null,
            ],
            'min_abp' => [
                'label' => 'Minimum ABP ($)',
                'type' => 'number',
                'value' => null,
            ],
            'max_abp' => [
                'label' => 'Maximum ABP ($)',
                'type' => 'number',
                'value' => null,
            ],
            'product_sub_categories' => [
                'label' => 'Product Sub Categories',
                'type' => 'select',
                'value' => null,
                'initialize' => 'GetAllSubCategories'
            ],
            'suppliers' => [
                'label' => 'Suppliers',
                'type' => 'select',
                'value' => null,
                'initialize' => 'getAllSuppliers'
            ],
            // 'take_rate_operand' => [
            //     'label' => 'Take Rate Pct must be',
            //     'type' => 'select',
            //     'placeholder' => 'Select operand',
            //     'value' => null,
            //     'options' => [
            //         '>',
            //         '<',
            //         '=',
            //         '>=',
            //         '<='
            //     ]
            // ],
            // 'take_rate_pct_value' => [
            //     'label' => 'Take Rate Pct',
            //     'type' => 'number',
            //     'value' => null,
            // ],
            // 'abp_operand' => [
            //     'label' => 'ABP must be',
            //     'type' => 'select',
            //     'placeholder' => 'Select operand',
            //     'value' => null,
            //     'options' => [
            //         '>',
            //         '<',
            //         '=',
            //         '>=',
            //         '<='
            //     ]
            // ],
            // 'abp_value' => [
            //     'label' => 'ABP',
            //     'type' => 'number',
            //     'value' => null,
            // ],
        ];

        return array_intersect_key($fields, $fields_keys);
    }

    public static function getReportsConfigs($reports_keys)
    {
        $reports = require 'protected/components/ReportsManagerConfig/ReportsManagerConfigs.php';

        return array_intersect_key($reports, $reports_keys);
    }

    public static function getAvailableReports()
    {
        return require 'protected/components/ReportsManagerConfig/ReportsManagerPermissions.php';
    }

    // -------------------------------------------------------------------------------- action_functions
    // --------------------- getGeneratableReports
    // --------------------- getGetSubscribedReports
    // --------------------- getGeneratedReports
    // --------------------- generateReport

    // list reports based on
    public static function getGeneratableReports()
    {
        $available_reports = self::getAvailableReports();
        $response = self::getReportsConfigs($available_reports);

        foreach ($response as $report_key => &$report_config) {
            $report_config['name_func'] = $report_key;
            $report_config['name'] = Yii::t('vuecomponents', "reports.name.{$report_key}");
        }
        uasort($response, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
        });

        return $response;
    }

    public static function getSubscribedReports()
    {
        $response = Yii::app()->rodb->createCommand(
            "SELECT 
                * 
            FROM 
                user_report_subscriptions 
            WHERE 
                user_id = :user_id"
        )->bindValue(':user_id', Yii::app()->user->id)->queryAll();

        $response = self::filterOutReportsWithMissingPermissions($response);
        foreach ($response as &$report) {
            $report['name'] = Yii::t('vuecomponents', "reports.name.{$report['report_name']}");
            $report['send_every'] = '';
            $report['frequency'] = '';
            $report['dates_included'] = '';
            if($report['send_monday'] == '1') {
                $report['send_every'] .= Yii::t('vuecomponents', 'reports.subscribed.send_monday');
            } 
            if($report['send_tuesday'] == '1') {
                $report['send_every'] .= (empty($report['send_every']) ? '' : ', ') . Yii::t('vuecomponents', 'reports.subscribed.send_tuesday');
            } 
            if($report['send_wednesday'] == '1') {
                $report['send_every'] .= (empty($report['send_every']) ? '' : ', ') . Yii::t('vuecomponents', 'reports.subscribed.send_wednesday');
            } 
            if($report['send_thursday'] == '1') {
                $report['send_every'] .= (empty($report['send_every']) ? '' : ', ') . Yii::t('vuecomponents', 'reports.subscribed.send_thursday');
            } 
            if($report['send_friday'] == '1') {
                $report['send_every'] .= (empty($report['send_every']) ? '' : ', ') . Yii::t('vuecomponents', 'reports.subscribed.send_friday');
            } 
            if($report['send_saturday'] == '1') {
                $report['send_every'] .= (empty($report['send_every']) ? '' : ', ') . Yii::t('vuecomponents', 'reports.subscribed.send_saturday');
            } 
            if($report['send_sunday'] == '1') {
                $report['send_every'] .= (empty($report['send_every']) ? '' : ', ') . Yii::t('vuecomponents', 'reports.subscribed.send_sunday');
            } 
            if(empty($report['send_every'])) {
                $report['send_every'] .= Yii::t('vuecomponents', 'reports.subscribed.send_on_day_of_month', ['{number}' => $report['send_on_day_of_month']]);
                $report['frequency'] = Yii::t('vuecomponents', 'reports.subscribed.monthly');
            }
            if(empty($report['frequency'])) {
                $report['frequency'] = Yii::t('vuecomponents', 'reports.subscribed.weekly');
            }
            if($report['params_time_last_month'] == '1') {
                $report['dates_included'] = Yii::t('vuecomponents', 'reports.subscribed.params_time_last_month');
            } else if($report['params_time_last_week_monday_sunday'] == '1') {
                $report['dates_included'] = Yii::t('vuecomponents', 'reports.subscribed.params_time_last_week_monday_sunday');
            } else if($report['params_time_last_week_sunday_saturday'] == '1') {
                $report['dates_included'] = Yii::t('vuecomponents', 'reports.subscribed.params_time_last_week_sunday_saturday');
            } else if($report['params_time_last_year'] == '1') {
                $report['dates_included'] = Yii::t('vuecomponents', 'reports.subscribed.params_time_last_year');
            } else if(!empty($report['params_time_last_days'])) {
                $report['dates_included'] = Yii::t('vuecomponents', 'reports.subscribed.params_time_last_days', ['{number}' => $report['params_time_last_days']]);
            }
        }

        return $response;
    }

    public static function filterOutReportsWithMissingPermissions($subscribed_reports)
    {
        $available_reports = self::getAvailableReports();
        foreach ($subscribed_reports as $key => $report) {
           if(!array_key_exists($report['report_name'], $available_reports)) {
                unset($subscribed_reports[$key]);
                try {
                    Yii::app()->db->createCommand(
                        "DELETE FROM `user_report_subscriptions` WHERE `user_id` = :user_id AND `id` = :id"
                    )->bindValues([
                        ':user_id' => $report['user_id'],
                        ':id' => $report['id']
                    ])->execute();
                } catch(Exception $e) {}
           }
        }
        return $subscribed_reports;
    }

    // query all reports for history
    public static function getGeneratedReports($since = null)
    {
        $data = [];
        $available_reports = self::getGeneratableReports();
        if (!empty($available_reports)) {
            $criteria = new CDbCriteria();
            !empty($since) ? $criteria->addCondition("created_at >= {$since}") : null;
            $criteria->addInCondition('name_func', array_keys($available_reports));
            $criteria->with = ['users'];
            $criteria->order = 'created_at DESC';
            $criteria->limit = 100;
            $all_reports = ReportsLog::model()->findAll($criteria);
            $data = AppGlobal::activeRecordToArray($all_reports);
            foreach ($data as $key => &$value) {
                $value['name'] = !empty($available_reports[$value['name_func']]['name']) ? $available_reports[$value['name_func']]['name'] : $key;
                $value['status_string'] = ReportsLog::mapStatus($value['status']);
                $value['created_at_timestamp'] = strtotime($value['created_at']);
                $value['link'] = !empty($value['link']) ? Yii::app()->s3images->getImage('reportscsv', $value['link']) : '';
                $value['staff_id'] = isset($value['users']['user_email']) ? $value['users']['user_email'] : $value['staff_id'];
            }
        }

        return $data;
    }

    // insert has pending -> beanstalk job
    public static function generateReport($name_func, $params = [])
    {
        if (!array_key_exists($name_func, self::getAvailableReports()) && !file_exists("protected/components/ReportsManagerQueries/${name_func}.php")) {
            throw new Exception('Invalid report request');
        }

        $reports_log = new ReportsLog();
        $reports_log->name_func = $name_func;
        $reports_log->status = ReportsLog::PENDING;
        $reports_log->staff_id = Yii::app()->user->id;
        $reports_log->params = json_encode($params);
        $reports_log->created_at = date('Y-m-d H:i:s');
        if (!$reports_log->save()) {
            throw new Exception('Report save failed');
        }

        $p = new Pheanstalk_Pheanstalk(Yii::app()->params['beanstalk_server_ip'], Yii::app()->params['beanstalk_server_port']);
        $p->useTube('reports');
        $w = new ReportsGenerationJob($reports_log->reports_log_id);
        $job = serialize($w);
        $p->put($job);
    }

    // -------------------------------------------------------------------------------- query_functions
    // --------------------- teamMemberWarnings
    // --------------------- yearlyGivebackUpToDate
    // --------------------- gmPerCategory
    // --------------------- teamMembersReportOnBreaks
    // --------------------- ratingsPerProduct
    // --------------------- listOfPUPs
    // --------------------- marginPerConsumedLot
    // --------------------- fullRouteSteps
    // --------------------- donationProgramStatisticsAllTime
    // --------------------- donationProgramStatisticsForSpecificPeriod
    // --------------------- receptionErrors
    // --------------------- emailsOfActiveLufavores
    // --------------------- ageOfInventory
    // --------------------- numberOfShorts
    // --------------------- salesFromDiscounts
    // --------------------- creditsAndRefundsWithRevertedTransactions
    // --------------------- remainingTicketsPerCommunityGroup
    // --------------------- refunds
    // --------------------- linesPerHour
    // --------------------- statsReminderToActivate
    // --------------------- statsReminderToCustomize
    // --------------------- receptionPutaways
    // --------------------- receptionWeights
    // --------------------- portioningInfo
    // --------------------- numberOfBasketsShipped
    // --------------------- assetsScannedBetweenTwoDates
    // --------------------- eufStrategy
    // --------------------- subscriptionsPerWeekday
    // --------------------- eufQuery
    // --------------------- numberOfInventoryChecks
    // --------------------- flawlessPerWeek
    // --------------------- flawlessPerDay
    // --------------------- salesFromDiscountGroupByCategory
    // --------------------- recipesSold
    // --------------------- allPostalCodes
    // --------------------- salesReportForAGivenDate
    // --------------------- detailsForRefundsOfTypeQualityComplains
    // --------------------- detailsForRefundsOfTypeQualityComplains33
    // --------------------- shorts
    // --------------------- deliveriesWithLatenessRawData
    // --------------------- deliveriesWithLatenessAggregatedData
    // --------------------- deliveriesWithLatenessDataByRoute
    // --------------------- assetsScannedPerWeek
    // --------------------- icePackUsage
    // --------------------- incompletedSubcriptionsThatGotCompleted
    // --------------------- productsRatingsOverTime
    // --------------------- usersPerCommunity
    // --------------------- missputReport
    // --------------------- allInventoryActivities7days
    // --------------------- usesOfCompletedCouponCode
    // --------------------- cancellationStats
    // --------------------- cancellationLogs
    // --------------------- productsDimensions
    // --------------------- numberOfUnitsInInventoryPerProduct
    // --------------------- packingErrorReport
    // --------------------- pupsAllOrders
    // --------------------- ordersAndBoxesPerWeekForPUPAndHD
    // --------------------- creditsGivenToPUPCoordinatorsPerWeek
    // --------------------- pupCreditTransactions
    // --------------------- giftCertificatePurchasedPerDay
    // --------------------- superLufavoreCount
    // --------------------- superGivebackRateBreakdown
    // --------------------- takeRateAfterGoingSuperLufavore
    // --------------------- abpAfterGoingSuperLufavore
    // --------------------- fullPUPReport
    // --------------------- automatedRefundsReceptionShortsQualityDidntMeetOurStandardPerWeek
    // --------------------- refundsPerformedByCSPerWeek
    // --------------------- callListCanceledSubscriptionsBetween2dates
    // --------------------- retentionDueToLTV
    // --------------------- roughNumberOfSupersAtSignUpBetween2Dates
    // --------------------- commsOptIn
    // --------------------- referralStats
    // --------------------- couponCostsPct
    // --------------------- couponCosts
    // --------------------- referralsPerUser
    // --------------------- campaignReferralStats
    // --------------------- naaTracking
    // --------------------- signUpFlowABRevenueTracking
    // --------------------- signUpFlowABPercentUsersReachingStep3
    // --------------------- signUpFlowABPercentOfSupers
    // --------------------- signUpFlowABCountSupers
    // --------------------- inventoryTurnOver
    // --------------------- hoursPerRole
    // --------------------- usersRole
    // --------------------- dailyRPCSupplierForecastedNeed
    // --------------------- papPutWeights
    // --------------------- driverBreaks
    // --------------------- emailMetrics
    // --------------------- lufavoreSwitch
    // --------------------- outerRegionPerformance
    // --------------------- communityEventsExpenseReport
    // --------------------- locksByDay
    // --------------------- locksByEmployee
    // --------------------- ordersByTranche
    // --------------------- salesByProductPerBasketType
    // --------------------- salesByProductWithFeatured
    // --------------------- inventoryPerLot
    // --------------------- liveInventoryForAudit
    //---------------------- portioningCostByProduct
    //---------------------- lufaFarmsAbsenceRequestTool
    //---------------------- lufaFarmsDailyMoodChecker
    //---------------------- lufaFarmsHRSurveyQuestion
    //---------------------- dailyAvailabilityTracker
    //---------------------- resolvedTicketsPerAgentNotBelongsToTheirComm
    //---------------------- rpcBillingReport
    //---------------------- rpcBillingNetsuiteReport
    //---------------------- lufaFarmsHrPortalAudit

    public static function internalOrdersReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ 	o.user_id AS 'User ID',
                    u.user_email AS 'User email',
                    o.delivery_date AS 'Order date',
                    od.product_id AS 'Product ID',
                    p.name AS 'Product name (fr)',
                    pl.l_name AS 'Product name (en)',
                    s.name AS 'Supplier name (fr)',
                    sl.l_name AS 'Supplier name (en)',
                    od.defined_retail_price_per_unit_for_default_weight AS 'Theorical price',
                    od.purchase_price_per_unit AS 'Cost',
                    IF(od.paid_price_for_real_weight > 0, od.paid_price_for_real_weight, od.paid_price_per_unit_for_default_weight)  AS 'Price paid',
                    IF(od.paid_price_for_real_weight > 0, (od.national_tax_amount_for_real_weight+od.provincial_tax_amount_for_real_weight), (od.national_tax_amount_per_unit_for_default_weight+od.provincial_tax_amount_per_unit_for_default_weight)) AS 'Taxes amount',
                    od.consigne_amount AS 'Consigne amount'
                FROM 
                    order_details od
                INNER JOIN orders o ON (o.status = 4 AND o.delivery_date BETWEEN :start AND :end AND o.user_id IN (87754, 213817, 152146, 169939, 219109, 219107, 333052, 412072, 19390, 19391, 19392, 19394, 19395, 559027, 559028, 559029, 559030, 559032, 559033, 559034, 559036, 559038, 559039, 559041) AND od.order_id = o.order_id)
                LEFT JOIN users u ON (o.user_id = u.user_id)
                LEFT JOIN products p ON (p.product_id = od.product_id)
                LEFT JOIN productsLang pl ON (pl.product_id = od.product_id AND pl.lang_id = 'en')
                LEFT JOIN suppliers s ON (s.supplier_id = p.supplier_id)
                LEFT JOIN suppliersLang sl ON (sl.supplier_id = p.supplier_id AND sl.lang_id = 'en')
                ORDER BY 
                    o.delivery_date ASC,
                    o.order_id ASC,
                    od.product_id ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function teamMemberWarnings($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $group_by = self::getGroupByForQuery('default_group_by', $params['default_group_by'], 'date');
        $cond_select = '';
        $group_by_string = '';
        $condition = '';
        if (!empty($group_by)) {
            $cond_select = ", u.user_email as 'Email', {$group_by} as '{$params['default_group_by']}', COUNT(*) as 'Total Warnings'";
            $group_by_string = " GROUP BY {$group_by}, ew.employee_id";
        } else {
            $cond_select = ", u.user_email as 'Email', date(ew.date) as 'Date Received', CONCAT(m.first_name, ' ', m.last_name) as 'Manager Name', m.user_email as 'Email', ew.comment as 'Comment'";
        }
        if (!empty($params['employee_ids'])) {
            $ids_string = self::getIdStringFromSelect($params['employee_ids']);
            $condition = " AND ew.employee_id IN ({$ids_string})";
        }
        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ CONCAT(u.first_name, ' ', u.last_name) as 'Employee Name',
                    u.internal_employee_id as 'EmployeurD'
                    {$cond_select}
                FROM 
                    employee_warnings ew
                INNER JOIN
                    users u ON u.user_id = ew.employee_id
                INNER JOIN
                    users m ON m.user_id = ew.manager_id
                WHERE 
                    date(ew.date) BETWEEN :start AND :end
                    {$condition}
                {$group_by_string}";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function packersWhoPackedEmployeeOrders($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ o.delivery_date AS 'Packing Date',
                    CONCAT(c.first_name, ' ', c.last_name) AS 'Client Name',
                    o.user_id AS 'Client ID',
                    ttp.order_id AS 'Order ID', 
                    o.total_order_amount AS 'Order Amount',
                    ttp.box_number AS 'Box Position',
                    SUM(IF(ttp.`status`=1, `quantity_put`, 0)) AS 'Number Of Items Packed',
                    t.task_id AS 'Task Number',
                    cart.serial AS 'Cart Serial',
                    t.finished_at AS 'Task Finished At',
                    CONCAT(p.first_name, ' ', p.last_name) AS 'Packer Name',
                    t.assigned_to AS 'Packer ID'
                FROM 
                    `task_type_prepbasket` ttp 
                INNER JOIN task t ON (t.task_id = ttp.task_id)
                INNER JOIN cart_activity ca ON (ca.task_id = t.task_id)
                INNER JOIN cart ON (cart.cart_id = ca.cart_id)
                INNER JOIN orders o ON (o.order_id = ttp.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start AND :end AND o.coupon_id = 15447)
                INNER JOIN users c ON (o.user_id = c.user_id)
                INNER JOIN users p ON (t.assigned_to = p.user_id)
                GROUP BY 
                    ttp.order_id,
                    ttp.box_number";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function deliveryRoutesWithDriversInfos($params)
    {
        if (empty($params['end'])) {
            throw new Exception('Missing date value');
        }

        // Stole query from function loadRouteDetailsWorkInProgress and adjusted it a bit
        $sql = "SELECT
                    /*+ MAX_EXECUTION_TIME(900000) */ :end AS Date,
                    IFNULL(companies.name, 'Not set') AS 'Contractor Name',
                    `vehicle`.`external_id` AS 'Vehicle Name',
                    lbri.departure_time AS 'Scheduled Departure Time',
                    `driverComms`.`name` AS 'Driver Name',
                    `driverComms`.`phone` AS 'Driver Phone Number'
                FROM 
                    `ww_routes` wwr 
                LEFT OUTER JOIN `ww_orders` wwo ON (wwo.`route_id`=wwr.`route_id`)  
                LEFT OUTER JOIN orders o ON (wwo.`order_number`=o.`order_id`)  
                LEFT OUTER JOIN `users` `users` ON (o.`user_id`=`users`.`user_id`) 
                LEFT OUTER JOIN `user_home_deliveries` `user_home_deliveries` ON (`user_home_deliveries`.`order_id`=o.`order_id`) 
                LEFT OUTER JOIN `ww_order_comments` `ww_order_comment` ON (`ww_order_comment`.`order_id`=wwo.`order_id`) 
                LEFT OUTER JOIN `drop_instance` `drop_instance` ON (`drop_instance`.`drop_instance_id`= wwo.`drop_instance_id`) 
                LEFT OUTER JOIN `drop_instance` `hd_di` ON (`hd_di`.`drop_instance_id`= o.`drop_instance_id`) 
                LEFT OUTER JOIN `droppoints` `droppoints` ON (`drop_instance`.`droppoint_id`=`droppoints`.`droppoint_id`) 
                LEFT OUTER JOIN (
                    SELECT 
                        droppoint_id, 
                        COUNT(orders.order_id) AS nb_orders,
                        SUM(IF(gift_receiver_id IS NULL, number_box_needed, 0)) AS box_needed,
                        SUM(IF(gift_receiver_id IS NOT NULL, number_box_needed, 0)) AS box_needed_gift
                    FROM 
                        orders
                    WHERE
                        status = 4 AND delivery_date = :end
                    GROUP BY 
                        droppoint_id
                ) `pup_orders` ON (`droppoints`.`droppoint_id`=`pup_orders`.`droppoint_id`)
                LEFT OUTER JOIN load_basket_route_info lbri ON (lbri.ww_route_id = wwr.`route_id` AND lbri.date = :end AND lbri.wave = 1)
                LEFT JOIN (
                    SELECT 
                        lbri_id,
                        SUM(IF(task.status='Done', 1, 0)) AS nb_loaded,
                        IFNULL(COUNT(task.task_id),0) AS nb_to_load,
                        DATE_FORMAT(MAX(task.finished_at), '%H:%i') AS loading_time
                    FROM 
                        task 
                    INNER JOIN load_basket_route_info lbri ON (lbri.date = :end AND lbri.wave = 1)
                    INNER JOIN `task_type_loadbasket` ON (task_type_loadbasket.`lbri_id` = lbri.route_info_id AND task_type_loadbasket.task_id = task.task_id)
                    GROUP BY 
                        lbri_id
                ) loading_status ON (loading_status.lbri_id = lbri.route_info_id)
                LEFT OUTER JOIN `ww_steps` `ww_steps_all` ON (`ww_steps_all`.`order_id`=wwo.`order_id`) 
                LEFT OUTER JOIN `ww_depots` `depot` ON (`depot`.`depot_id`=`ww_steps_all`.`depot_id`) 
                LEFT OUTER JOIN `user_comms` `driverComms` ON (`driverComms`.`user_id`=`ww_steps_all`.`user_id` AND driverComms.main = 1) 
                LEFT JOIN users drivers ON (drivers.user_id = `ww_steps_all`.`user_id`)
                LEFT JOIN companies ON (companies.company_id = drivers.company_id)
                LEFT OUTER JOIN `ww_vehicles` `vehicle` ON (`vehicle`.`vehicle_id`=wwr.`vehicle_id`) 
                LEFT OUTER JOIN (SELECT ww_step_id, COUNT(*) as violation_count FROM violations WHERE violation_date = :end GROUP BY ww_step_id) v ON v.ww_step_id = ww_steps_all.step_id
                WHERE 
                    (wwr.date =:end) AND ww_steps_all.step_id IS NOT NULL
                GROUP BY 
                    wwr.route_id
                ORDER BY 
                    lbri.departure_time ASC,
                    wwr.route_id ASC, 
                    ww_steps_all.scheduled_datetime ASC,
                    ww_steps_all.order ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':end', $params['end'])->queryAll();
    }

    public static function yearlyGivebackUpToDate($params)
    {
        if (empty($params['end'])) {
            throw new Exception('Missing date value');
        }

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ supervores_incentive_log.user_id AS 'Customer ID', 
                    users.first_name AS 'Customer First Name', 
                    users.last_name AS 'Customer Last Name', 
                    ROUND(supervores_incentive_log.current_earnings, 2) AS 'Earnings amount ($)', 
                    IF(DATE(supervores_incentive_log.updated_at) = '0000-00-00', DATE(supervores_incentive_log.created_at), DATE(supervores_incentive_log.updated_at)) AS 'Date',
                    supervores_incentive_log.pct AS 'Giveback percentage (%)',
                    ROUND(supervores_incentive_log.projected_earnings, 2) AS 'Projected Earnings amount EOY ($)', 
                    users.giveback_donation_percent * 100 AS 'Giveback Donation (%)',
                    ROUND(supervores_incentive_log.projected_earnings * users.giveback_donation_percent, 2) AS 'Projected Giveback Donation amount EOY ($)'
                FROM 
                    securelufacom.`supervores_incentive_log` 
                INNER JOIN securelufacom.users ON (users.user_id = supervores_incentive_log.user_id) 
                WHERE 
                    supervores_incentive_log.subscription_status = 1 AND 
                    supervores_incentive_log.subscription_type = 0 AND
                    WEEK(:end, 3) = supervores_incentive_log.`iso_week` AND 
                    YEAR(:end) = supervores_incentive_log.`year`";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':end', $params['end'])->queryAll();
    }

    public static function availabilityPerSubSubcategory($params) {

        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ IFNULL(pcl.l_name, pc.name) AS 'Category name',
                    IFNULL(pscl.l_name, psc.name)	AS 'Subcategory name',
                    IFNULL(psscl.l_name, pssc.name) AS 'Sub Subcategory name',
                    YEARWEEK(subquery.delivery_date, 6) AS week,
                    YEAR(subquery.delivery_date) AS year,
                    ROUND(subquery.available_global/subquery.checks*100, 2) AS 'Total availability %',
                    ROUND(subquery.available_lufa/subquery.checks*100, 2) AS 'GH availability %',
                    ROUND(subquery.available_3rdparty/subquery.checks*100, 2) AS '3rd party availability %'
                FROM (
                    SELECT 
                        sas.delivery_date,
                        sas.subsubcategory_id,
                        SUM(IF(sas.nb_products_available_lufa > 0 OR sas.nb_products_available_3rdparty > 0, 1, 0)) AS available_global,
                        SUM(IF(sas.nb_products_available_lufa > 0, 1, 0)) AS available_lufa,
                        SUM(IF(sas.nb_products_available_3rdparty > 0, 1, 0)) AS available_3rdparty,
                        COUNT(sas.subsubcategory_availability_stats_id) AS checks
                    FROM 
                        subsubcategory_availability_stats sas
                    WHERE
                        sas.delivery_date BETWEEN :start AND :end
                    GROUP BY 
                        sas.delivery_date,
                        sas.subsubcategory_id
                ) subquery
                INNER JOIN productSubSubCategories AS pssc ON pssc.sub_sub_id = subquery.subsubcategory_id
                INNER JOIN productSubCategories AS psc ON pssc.subcategory_id = psc.subcategory_id
                INNER JOIN product_categories AS pc ON psc.category_id = pc.category_id
                LEFT JOIN productSubCategoriesLang pscl ON (psc.subcategory_id = pscl.subcategory_id AND pscl.lang_id = 'en')
                LEFT JOIN productCategoriesLang pcl ON (pc.category_id = pcl.category_id AND pcl.lang_id = 'en')
                LEFT JOIN productSubSubCategoriesLang psscl ON (psscl.sub_sub_id = pssc.sub_sub_id AND psscl.lang_id = 'en')
                GROUP BY 
                    YEARWEEK(subquery.delivery_date, 6),
                    subquery.subsubcategory_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function productsWithUpcomingCustomTagsOnMarketplace($params) {

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ p.product_id AS 'Product ID',
                    p.name AS 'Product name (fr)',
                    pl.l_name AS 'Product name (en)',
                    s.name AS 'Supplier name',
                    pc.name AS 'Category name (fr)',
                    pcl.l_name AS 'Category name (en)',
                    psc.name AS 'Subcategory name (fr)',
                    pscl.l_name AS 'Subcategory name (en)',
                    pssc.name AS 'Sub Subcategory name (fr)',
                    psscl.l_name AS 'Sub Subcategory name (en)',
                    mpt.marketplace_product_tag_start_date AS 'Custom label start date',
                    mpt.marketplace_product_tag_end_date AS 'Custom label end date',
                    mpt.marketplace_product_tag_card_label_fr AS 'Custom label (fr)',
                    mpt.marketplace_product_tag_card_label_en AS 'Custom label (en)'
                FROM
                    products p
                INNER JOIN marketplace_product_tag mpt ON (mpt.marketplace_product_tag_product_id = p.product_id AND mpt.marketplace_product_tag_end_date >= CURDATE())
                LEFT JOIN productsLang pl ON (pl.product_id = p.product_id AND pl.lang_id = 'en')
                LEFT JOIN suppliers s ON (s.supplier_id = p.supplier_id)
                INNER JOIN productSubSubCategories AS pssc ON pssc.sub_sub_id = p.sub_sub_id
                INNER JOIN productSubCategories AS psc ON pssc.subcategory_id = psc.subcategory_id
                INNER JOIN product_categories AS pc ON psc.category_id = pc.category_id
                LEFT JOIN productSubCategoriesLang pscl ON (psc.subcategory_id = pscl.subcategory_id AND pscl.lang_id = 'en')
                LEFT JOIN productCategoriesLang pcl ON (pc.category_id = pcl.category_id AND pcl.lang_id = 'en')
                LEFT JOIN productSubSubCategoriesLang psscl ON (psscl.sub_sub_id = pssc.sub_sub_id AND psscl.lang_id = 'en')
                ORDER BY
                    mpt.marketplace_product_tag_start_date ASC
                ";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function greenhouseQuantityOrderedVsReceived($params) {

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ YEARWEEK(sfid.delivery_date, 6) as 'Year-Week',
                    p.product_id AS 'Product ID',
                    p.name AS 'Product name (fr)',
                    pl.l_name AS 'Product name (en)',
                    s.name AS 'Supplier name',
                    p.weight AS 'Format (grams)',
                    SUM(number_of_units_ordered) AS 'Units ordered',
                    SUM(quantity_accepted) AS 'Units received',
                    COUNT(supplier_forecast_orders_id) AS 'Nb of SFIDs'
                FROM 
                    supplier_forecast_orders sfid
                INNER JOIN products p ON (p.product_id = sfid.product_id)
                LEFT JOIN productsLang pl ON (pl.product_id = p.product_id AND pl.lang_id = 'en')
                INNER JOIN suppliers s ON (p.supplier_id = s.supplier_id)
                WHERE 
                    p.supplier_id IN (1, 113,376,597,922,972) AND 
                    sfid.delivery_date BETWEEN :start AND :end AND 
                    sfid.status IN (:STATUS_ARRIVED, :STATUS_VALIDATION_CONFIRMED, :STATUS_REJECTED, :STATUS_CONFIRMED)
                GROUP BY 
                    YEARWEEK(sfid.delivery_date, 6), 
                    p.product_id
                ORDER BY 
                    YEARWEEK(sfid.delivery_date, 6), 
                    p.supplier_id, 
                    p.product_id
                ";

        return Yii::app()->rodb->createCommand($sql)->bindValues(
            [
                ':start' => $params['start'],
                ':end' => $params['end'],
                ':STATUS_ARRIVED' => SupplierForecastOrders::STATUS_ARRIVED,
                ':STATUS_VALIDATION_CONFIRMED' => SupplierForecastOrders::STATUS_VALIDATION_CONFIRMED,
                ':STATUS_REJECTED' => SupplierForecastOrders::STATUS_REJECTED,
                ':STATUS_CONFIRMED' => SupplierForecastOrders::STATUS_CONFIRMED,
            ]
        )->queryAll();
    }

    public static function gmPerCategory($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = 'SELECT 
        /*+ MAX_EXECUTION_TIME(900000) */ CONCAT(:start, " - ", :end) AS "Period",
        t.name AS "Category Name", 
        ROUND((t.u/(SELECT COUNT(order_id) as so FROM orders WHERE status = 4 AND delivery_date BETWEEN :start AND :end)), 2) AS "Units per Basket", 
        t.rev AS "Total Revenue", 
        t.cost AS "Total Cost", 
        ROUND(t.rev/(SELECT COUNT(order_id) as so FROM orders WHERE status = 4 AND delivery_date BETWEEN :start AND :end),2) AS "Contribution", 
        (SELECT COUNT(order_id) as so FROM orders WHERE status = 4 AND delivery_date BETWEEN :start AND :end) AS "Basket shipped", 
        ROUND(((t.rev-t.cost) / t.rev),3) AS "GM" 
    FROM 
        (
        SELECT 
            YEARWEEK(delivery_date, 3) as w,
            pc.name, 
            COUNT(od.order_details_id) as u, 
            SUM(od.defined_retail_price_per_unit_for_default_weight) as rev, 
            SUM(od.purchase_price_per_unit) as cost
        FROM 
            `order_details` od
        LEFT JOIN orders o ON (o.order_id = od.order_id)
        LEFT JOIN products p ON (p.product_id = od.product_id)
        LEFT JOIN productSubSubCategories ssc ON ssc.sub_sub_id = p.sub_sub_id
        LEFT JOIN productSubCategories psc ON (psc.subcategory_id = ssc.subcategory_id)
        LEFT JOIN product_categories pc ON (pc.category_id = psc.category_id)
        LEFT JOIN suppliers s ON (p.supplier_id = s.supplier_id)
        WHERE 
            o.status = 4 AND 
            o.delivery_date BETWEEN :start AND :end AND p.supplier_id NOT IN (SELECT supplier_id FROM `suppliers` WHERE `name` LIKE "%Lufa%")
        GROUP BY 
            pc.name 
        ) t 
    UNION
    SELECT
        CONCAT(:start, " - ", :end) AS "Period",
        "Recipes (Excluding Lufa Products)" AS "Category Name",
        "-" AS "Units per Basket", 
        t.rev AS "Total Revenue",
        "-" AS "Total Cost", 
        ROUND(t.rev/(SELECT COUNT(order_id) as so FROM orders WHERE status = 4 AND delivery_date BETWEEN :start AND :end),2) AS "Contribution", 
        "-" AS "Basket shipped", 
        ROUND(((t.rev-t.cost) / t.rev),3) AS "GM"
    FROM 
        (
        SELECT 
            YEARWEEK(delivery_date, 3) as w,
            COUNT(od.order_details_id) as u, 
            SUM(od.defined_retail_price_per_unit_for_default_weight) as rev, 
            SUM(od.purchase_price_per_unit) as cost
        FROM 
            `order_details` od
        LEFT JOIN orders o ON (o.order_id = od.order_id AND od.recipe_id IS NOT NULL)
        LEFT JOIN products p ON (p.product_id = od.product_id)
        LEFT JOIN productSubSubCategories ssc ON ssc.sub_sub_id = p.sub_sub_id
        LEFT JOIN productSubCategories psc ON (psc.subcategory_id = ssc.subcategory_id)
        LEFT JOIN product_categories pc ON (pc.category_id = psc.category_id)
        LEFT JOIN suppliers s ON (p.supplier_id = s.supplier_id)
        WHERE 
            o.status = 4 AND 
            o.delivery_date BETWEEN :start AND :end AND p.supplier_id NOT IN (SELECT supplier_id FROM `suppliers` WHERE `name` LIKE "%Lufa%")
        ) t 
    UNION
    SELECT
        CONCAT(:start, " - ", :end) AS "Period",
        "Recipes (Including Lufa Products)" AS "Category Name",
        "-" AS "Units per Basket", 
        t.rev AS "Total Revenue",
        "-" AS "Total Cost", 
        ROUND(t.rev/(SELECT COUNT(order_id) as so FROM orders WHERE status = 4 AND delivery_date BETWEEN :start AND :end),2) AS "Contribution", 
        "-" AS "Basket shipped", 
        ROUND(((t.rev-t.cost) / t.rev),3) AS "GM"
    FROM 
        (
        SELECT 
            YEARWEEK(delivery_date, 3) as w,
            COUNT(od.order_details_id) as u, 
            SUM(od.defined_retail_price_per_unit_for_default_weight) as rev, 
            SUM(od.purchase_price_per_unit) as cost
        FROM 
            `order_details` od
        LEFT JOIN orders o ON (o.order_id = od.order_id AND od.recipe_id IS NOT NULL)
        LEFT JOIN products p ON (p.product_id = od.product_id)
        LEFT JOIN productSubSubCategories ssc ON ssc.sub_sub_id = p.sub_sub_id
        LEFT JOIN productSubCategories psc ON (psc.subcategory_id = ssc.subcategory_id)
        LEFT JOIN product_categories pc ON (pc.category_id = psc.category_id)
        LEFT JOIN suppliers s ON (p.supplier_id = s.supplier_id)
        WHERE 
            o.status = 4 AND 
            o.delivery_date BETWEEN :start AND :end
        ) t
    UNION 
    SELECT
        CONCAT(:start, " - ", :end) AS "Period",
        "Bundles" AS "Category Name",
        "-" AS "Units per Basket", 
        t.rev AS "Total Revenue",
        "-" AS "Total Cost", 
        ROUND(t.rev/(SELECT COUNT(order_id) as so FROM orders WHERE status = 4 AND delivery_date BETWEEN :start AND :end),2) AS "Contribution", 
        "-" AS "Basket shipped", 
        ROUND(((t.rev-t.cost) / t.rev),3) AS "GM"
    FROM 
        (
        SELECT 
            YEARWEEK(delivery_date, 3) as w,
            COUNT(od.order_details_id) as u, 
            SUM(od.defined_retail_price_per_unit_for_default_weight) as rev, 
            SUM(od.purchase_price_per_unit) as cost
        FROM 
            `order_details` od
        LEFT JOIN orders o ON (o.order_id = od.order_id AND od.recipe_id IS NOT NULL)
        LEFT JOIN products p ON (p.product_id = od.product_id)
        LEFT JOIN productSubSubCategories ssc ON ssc.sub_sub_id = p.sub_sub_id
        LEFT JOIN productSubCategories psc ON (psc.subcategory_id = ssc.subcategory_id)
        LEFT JOIN product_categories pc ON (pc.category_id = psc.category_id)
        LEFT JOIN suppliers s ON (p.supplier_id = s.supplier_id)
        INNER JOIN recipes r ON od.recipe_id = r.recipe_id AND r.is_bundle = 1 
        WHERE 
            o.status = 4 AND 
            o.delivery_date BETWEEN :start AND :end
        ) t ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function teamMembersReportOnBreaks($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = 'SELECT
                    /*+ MAX_EXECUTION_TIME(900000) */ DATE(rs.`adjusted_start_time`) AS "Working Date",
                    rs.employee_id AS "Employee ID",
                    CONCAT(users.first_name, " ", users.last_name) AS "Employee Name",
                    users.internal_employee_id AS "EmployerD #", 
                    DATE_FORMAT(rs.`adjusted_start_time`, "%H:%i") AS "Sched. Start Time",
                    DATE_FORMAT(rs.`adjusted_end_time`, "%H:%i") AS "Sched. End Time",
                    SUM(IF(punches.stop LIKE "%23:59:59" OR punches.stop IS NULL, 0, 1)) AS "Number of punch out",
                    DATE_FORMAT(MIN(punches.start), "%H:%i") AS "Punched in",
                    DATE_FORMAT(MAX(punches.stop), "%H:%i") AS "Punched out",
                    TIME_FORMAT(SEC_TO_TIME(SUM(punches.punch_time_in_sec)),"%H:%i" ) AS "Time punched in",
                    TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, MIN(punches.start), MAX(punches.stop))),"%H:%i" ) AS "Time on site",
                    TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, MIN(punches.start), MAX(punches.stop)) - SUM(punches.punch_time_in_sec)),"%H:%i" ) AS "Break time"
                FROM 
                    `root_scheduling` rs 
                INNER JOIN users ON (users.user_id = rs.employee_id)
                LEFT JOIN (
                    SELECT 
                        payment_logging_id, 
                        user_id, 
                        start,
                        stop,
                        DATE(root_scheduling.adjusted_start_time) AS Applicable_on_date,
                        TIMESTAMPDIFF(SECOND, start, stop) AS punch_time_in_sec
                    FROM 
                        payment_logging
                    LEFT JOIN root_scheduling ON (root_scheduling.employee_id = payment_logging.user_id AND start BETWEEN root_scheduling.adjusted_start_time AND root_scheduling.adjusted_end_time)
                ) as punches ON (punches.user_id = rs.employee_id AND punches.Applicable_on_date = DATE(rs.`adjusted_start_time`))
                WHERE 
                    DATE(rs.`adjusted_start_time`) BETWEEN :start AND :end
                GROUP BY 
                    DATE(rs.`adjusted_start_time`),
                    rs.employee_id';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function ratingsPerProduct($params)
    {
        $sql = '
            SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ s.name AS "Supplier Name",
                p.product_id AS "Product ID",
                p.name AS "Product Name",
                CONCAT(u.first_name, " ", u.last_name) AS "Purchaser",
                CONCAT(u1.first_name, " ", u1.last_name) AS "Category Manager",
                ratings.average AS "Current Rating",
                ratings.nb AS "# Ratings"
            FROM 
                products p
            INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
            INNER JOIN productSubSubCategories pssc ON (pssc.sub_sub_id = p.sub_sub_id)
            INNER JOIN productSubCategories psc ON (psc.subcategory_id = pssc.subcategory_id)
            LEFT JOIN users u ON (u.user_id = p.purchaser_id)
            LEFT JOIN users u1 ON (u1.user_id = p.category_manager_id)
            LEFT JOIN (
                SELECT 
                    product_id, 
                    ROUND(AVG(rating),0) AS average,
                    COUNT(product_user_rating_id) AS nb 
                FROM 
                    product_user_ratings
                WHERE 
                    product_user_ratings.updated_at >= SUBDATE(CURDATE(), INTERVAL 270 DAY)
                GROUP BY 
                    product_id
            ) ratings ON (ratings.product_id = p.product_id) 
            WHERE 
                p.status = 1';

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function listOfPUPs($params)
    {
        $sql = "
        SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ d.droppoint_id AS 'Droppoint ID',
            d.name AS 'Droppoint Name',
            d.address AS 'Address',
            d.city_name AS 'City',
            d.zip_code AS 'Postal Code',
            d.coordinator_name AS 'Coordinator Name',
            d.coordinator_email AS 'Coordinator Email',
            d.coordinator_phone AS 'Coordinator Phone',
            CONCAT(first_name, ' ', last_name) AS 'PUP Primary Coordinator',
            u.user_id AS 'Primary User ID',
            CASE 
                WHEN u.status = 1 THEN 'Active'
                ELSE 'Inactive'
            END AS 'Status',
            u.user_email AS 'Primary E-mail',
            di.drop_instance_id AS 'Drop-instance ID',
            di.delivery_day AS 'Delivery Day',
            di.capacity AS 'Capacity',
            di.take_rate AS 'Take Rate',
            di.take_rate_capacity AS 'Take Rate Capacity',
            di.opening_time AS 'DI Opening Time',
            di.closing_time AS 'DI Closing Time',
            di.business_opening_time AS 'Business Opening Time',
            di.business_closing_time AS 'Business Closing Time',
            CONCAT(b.name, ' (min $', b.min_credits, ')') AS 'Min. basket price'
        FROM 
            `droppoints` d 
        INNER JOIN drop_instance di ON (di.droppoint_id = d.droppoint_id)
        LEFT JOIN (
            SELECT 
                DISTINCT `droppoints_id`, 
                user_id 
            FROM 
                droppoints_coordinators 
            WHERE 
                primary_coordinator = 1
        ) dc ON (dc.droppoints_id = d.droppoint_id)
        LEFT JOIN users u on dc.user_id = u.user_id
        INNER JOIN baskets b on d.droppoints_basket_id = b.basket_id
        WHERE
            di.capacity>0
        ORDER BY d.droppoint_id;";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function ghProductsRefund($params) {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ s.name AS 'Greenhouse',
                p.name AS 'Product name',
                p.product_id AS 'Product ID',
                ROUND(od.paid_price_per_unit_for_default_weight * od.refunded_qty, 2) AS 'Refund amount',
                od.refunded_reason AS 'Refund reason',
                IFNULL(od.refund_comment, '') AS 'Refund comment'
            FROM 
                order_details od
            JOIN orders o ON (od.order_id = o.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start AND :end)
            JOIN products p ON (od.product_id = p.product_id AND p.supplier_id IN (1,113,376,597,922,972)) 
            JOIN suppliers s ON (p.supplier_id = s.supplier_id) 
            WHERE 
                od.refunded_qty > 0
            ORDER BY 
                p.product_id ASC";

        $data = Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();

        foreach ($data as $key => &$row) {
            $row['Refund reason'] = is_array(OrderDetails::loadRefundoptions($row['Refund reason'])) ? '-' : OrderDetails::loadRefundoptions($row['Refund reason']);         
        }

        return $data;
    }

    public static function marginPerConsumedLot($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ emptied_on AS 'Emptied On',
            nb_days_in_warehouse AS '# days in WH',
            lot_id AS 'Lot ID',
            product_id AS 'Product ID',
            product_name AS 'Product Name',
            supplier_name AS 'Supplier Name',
            IF(charge_by_weight = 1, 'Yes', 'No') AS 'Charge By Weight?',
            purchaser_name AS 'Purch Name',
            category_manager_name AS 'Cat Manager Name',
            initial_quantity AS 'Qty Received (unit)',
            ROUND(weight_received_in_kg,1) AS 'Weight Received (kg)',
            ROUND(initial_cost) AS 'Cost ($)',
            ROUND(theorical_revenue_bb) AS 'Theo. Revenue ($)',
            (ROUND((theorical_revenue_bb-initial_cost)/theorical_revenue_bb, 4)*100) AS 'Theo. Margin (%)',
            qty_sold_and_packed AS 'Qty Packed (unit)',
            ROUND(IF(sales_real_weight = 0, revenue_based_on_bb_settings, sales_real_weight)) AS 'Real Revenue ($)',
            ROUND(ROUND((IF(sales_real_weight = 0, revenue_based_on_bb_settings, sales_real_weight)-initial_cost)/IF(sales_real_weight = 0, revenue_based_on_bb_settings, sales_real_weight), 4)*100,2) AS 'Real Margin (%)',
            ROUND(ROUND(theorical_revenue_bb)-ROUND(IF(sales_real_weight = 0, revenue_based_on_bb_settings, sales_real_weight))) AS 'Rev Losses ($)',
            ROUND(ROUND(ROUND((IF(sales_real_weight = 0, revenue_based_on_bb_settings, sales_real_weight)-initial_cost)/IF(sales_real_weight = 0, revenue_based_on_bb_settings, sales_real_weight), 4)*100,2)-(ROUND((theorical_revenue_bb-initial_cost)/theorical_revenue_bb, 4)*100),2) AS 'Losses Margin (%)'
        FROM (
            SELECT 
                i.emptied_on,
                CONCAT(purchaser.first_name, ' ', purchaser.last_name) AS purchaser_name,
                CONCAT(category_manager.first_name, ' ', category_manager.last_name) AS category_manager_name,
                p.charge_by_weight,
                DATEDIFF(i.emptied_on, i.created) AS nb_days_in_warehouse,
                i.lot_id,
                i.product_id,
                p.name AS product_name,
                s.name AS supplier_name,
                (i.initial_quantity - IFNULL(qty_transfered,0)) AS initial_quantity,
                IF(sfo.weight_accepted=0, ROUND(((i.initial_quantity - IFNULL(qty_transfered,0))*sfo.product_weight)/1000,2), IFNULL((i.initial_gross_weight/1000),sfo.weight_accepted)) AS weight_received_in_kg,
                CASE
                    WHEN sfo.reception_type = 4 AND i.initial_gross_weight>0 THEN ROUND((i.initial_quantity-IFNULL(qty_transfered,0))*sfo.price_per_unit,2)
                    WHEN sfo.reception_type = 4 AND i.initial_gross_weight IS NULL THEN ROUND((sfo.number_of_units_at_reception*sfo.reception_cost)-(IFNULL(qty_transfered,0)*sfo.price_per_unit),2)
                    WHEN sfo.reception_type = 3 THEN ROUND(((sfo.price_per_unit / sfo.product_weight * 1000) * IFNULL((i.initial_gross_weight/1000),sfo.net_weight_received))-(IFNULL(qty_transfered,0)*sfo.price_per_unit),2)
                    ELSE ROUND(((i.initial_quantity - IFNULL(qty_transfered,0))*sfo.price_per_unit),2)
                END AS initial_cost,
                ((i.initial_quantity - IFNULL(qty_transfered,0)) * p.price) AS theorical_revenue, /* TO CHANGE TO sfo.retail_price... */
                ((i.initial_quantity - IFNULL(qty_transfered,0)) * sales.sales_based_on_avg_bb_settings) as theorical_revenue_bb,
                sales.qty_sold_and_packed,
                sales.sales_based_on_bb_settings AS revenue_based_on_bb_settings,
                ROUND(sales.theorical_weight_to_pack,2) AS weight_invoiced_at_midnight,
                sales.sales_real_weight,
                sfo.reception_type,
                sfo.number_of_units_at_reception
            FROM 
                `inventory` i 
            INNER JOIN products p ON (p.product_id = i.product_id)
            INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
            INNER JOIN supplier_forecast_orders sfo ON (sfo.supplier_forecast_orders_id = i.supplier_forecast_orders_id)
            LEFT JOIN productSubSubCategories pssc ON (pssc.sub_sub_id = p.sub_sub_id)
            LEFT JOIN productSubCategories psc ON (psc.subcategory_id = pssc.subcategory_id)
            LEFT JOIN users purchaser ON (purchaser.user_id = p.purchaser_id)
            LEFT JOIN users category_manager ON (category_manager.user_id = p.category_manager_id)
            LEFT JOIN (
                SELECT 
                    inventory_activity.lot_id,
                    SUM(ABS(quantity)) AS qty_transfered 
                FROM 
                    `inventory_activity` 
                INNER JOIN inventory ON (inventory.lot_id = inventory_activity.lot_id AND inventory.emptied_on BETWEEN :start AND :end)
                WHERE 
                    inventory_activity.inventory_activity_type_id IN (109, 113)
                GROUP BY
                    inventory_activity.lot_id
            ) transfer_act ON (transfer_act.lot_id = i.lot_id)
            LEFT JOIN (
                SELECT 
                    picked_from_lot_id,
                    COUNT(order_details_id) AS qty_sold_and_packed,
                    SUM(order_details.defined_retail_price_per_unit_for_default_weight) AS sales_based_on_bb_settings,
                    AVG(order_details.defined_retail_price_per_unit_for_default_weight) AS sales_based_on_avg_bb_settings,
                    SUM(order_details.paid_price_for_real_weight) AS sales_real_weight,
                    AVG(order_details.paid_price_for_real_weight) AS avg_sales_real_weight,
                    SUM(order_details.default_weight_per_unit) AS theorical_weight_to_pack
                FROM 
                    `order_details` 
                INNER JOIN inventory ON (inventory.lot_id = picked_from_lot_id AND emptied_on BETWEEN :start AND :end)
                GROUP BY
                    picked_from_lot_id
            ) sales ON (sales.picked_from_lot_id = i.lot_id)	
            WHERE 
                i.emptied_on BETWEEN :start AND :end 
        ) tmp";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'].' 00:00:00')->bindValue(':end', $params['end'].' 23:59:59')->queryAll();
    }

    public static function packerFlagsByLot($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ pa.lot_id AS 'Lot ID',
            CASE
                WHEN products.conservation_mode_in_warehouse = 0 THEN 'AMBIENT'
                WHEN products.conservation_mode_in_warehouse = 1 THEN 'FRIDGE_4_DEGREES'
                WHEN products.conservation_mode_in_warehouse = 2 THEN 'FRIDGE_15_DEGREES'
                WHEN products.conservation_mode_in_warehouse = 3 THEN 'FREEZER'
                WHEN products.conservation_mode_in_warehouse = 4 THEN 'OFFSITE_FRIDGE_4_DEGREES'
                WHEN products.conservation_mode_in_warehouse = 5 THEN 'OFFSITE_FREEZER'
                WHEN products.conservation_mode_in_warehouse = 6 THEN 'FRIDGE_8_DEGREES'
                ELSE 'UNKNOWN - WEIRD'
            END AS 'Storage temperature at Lufa',
            products.name AS 'Product name (fr)',
            productsLang.l_name AS 'Product name (en)',
            suppliers.name AS 'Supplier',
            pa.picks AS '# of picks',
            SUM(IF(ttic.reason_id = 'Not found', ttic.nb_checks, 0)) AS 'Lot not found',
            SUM(IF(ttic.reason_id = 'Recount', ttic.nb_checks, 0)) AS 'Recount',
            SUM(IF(ttic.reason_id = 'Bad quality', ttic.nb_checks, 0)) AS 'Bad quality',
            SUM(IF(ttic.reason_id = 'Horrible quality', ttic.nb_checks, 0)) AS 'Horrible quality',
            SUM(IF(ttic.reason_id IN ('Not found','Recount','Bad quality','Horrible quality'), ttic.nb_checks, 0)) AS 'Total # of flags'
        FROM 
            (
                SELECT 
                    lot_id,
                    COUNT(pick_activities_id) as picks
                FROM 
                    pick_activities
                WHERE 
                    created_at BETWEEN :start AND :end
                GROUP BY 
                    lot_id
        ) pa
        INNER JOIN inventory ON (inventory.lot_id = pa.lot_id)
        INNER JOIN products ON (products.product_id = inventory.product_id)
        LEFT JOIN productsLang ON (productsLang.product_id = products.product_id AND productsLang.lang_id = 'en')
        INNER JOIN suppliers ON (products.supplier_id = suppliers.supplier_id)
        LEFT JOIN (
            SELECT 
                task_type_inventorycheck.lot_id,
                task_type_inventorycheck.reason_id,
                COUNT(task_type_inventorycheck.task_type_inventorycheck_id) as nb_checks
            FROM 
                `task` 
            INNER JOIN task_type_inventorycheck ON (task_type_inventorycheck.task_id = task.task_id)
            WHERE 
                `task_type_defaults_id` = 5 AND 
                `created_at` BETWEEN :start AND :end
            GROUP BY 
                task_type_inventorycheck.lot_id,
                task_type_inventorycheck.reason_id
        ) ttic ON (ttic.lot_id = pa.lot_id)
        GROUP BY 
            pa.lot_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'].' 00:00:00')->bindValue(':end', $params['end'].' 23:59:59')->queryAll();
    }

    public static function expiredItemsWeCanGiveForFreeToday($params, $productIds = [])
    {
        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ i.lot_id AS 'LOT ID',
                lh.display_location AS 'LOCATION',
                CONCAT(u.first_name, ' ', u.last_name) AS 'PURCHASER',
                CONCAT(u1.first_name, ' ', u1.last_name) AS 'CATEGORY MANAGER',
                i.product_id AS 'PRODUCT ID', 
                p.name AS 'PRODUCT NAME',
                p.price AS 'PRODUCT RETAIL PRICE BY DEFAULT',
                s.name AS 'SUPPLIER NAME',
                pc.name AS 'CATEGORY NAME',
                i.number_of_units_available AS 'QTY IN LOT', 
                DATEDIFF(CURDATE(), i.created) AS 'IN WH SINCE X DAYS',
                DATE_SUB(Expiry_date, INTERVAL p.days_remove_before_expiry_date DAY) AS 'SHOULD BE REMOVE FROM WH ON',
                sales.nb AS 'QTY SOLD TODAY',
                0 AS 'QTY WE CAN GIVE FOR FREE',
                0 AS 'LOSSES BASES ON RETAIL PRICE BY DEFAULT'
            FROM 
                inventory i
            LEFT JOIN products p ON p.product_id = i.product_id
            LEFT JOIN suppliers s ON (s.supplier_id = p.supplier_id)
            LEFT JOIN productSubSubCategories pssc on (p.sub_sub_id = pssc.sub_sub_id)
            LEFT JOIN productSubCategories psc on (pssc.subcategory_id = psc.subcategory_id)
            LEFT JOIN product_categories pc on (psc.category_id = pc.category_id)
            LEFT JOIN location_header lh ON (lh.location_header_id = i.location_header_id)
            LEFT JOIN users u ON (u.user_id = p.purchaser_id)
            LEFT JOIN users u1 ON (u1.user_id = p.category_manager_id)
            LEFT JOIN (
                SELECT 
                    od.product_id,
                    COUNT(od.order_details_id) AS nb
                FROM 
                    order_details od
                INNER JOIN orders o ON (o.order_id = od.order_id AND o.status = 4 AND o.delivery_date = CURDATE())
                GROUP BY 
                    od.product_id
            ) sales ON (sales.product_id = i.product_id)
            WHERE 
                `number_of_units_available` > 0 AND 
                DATE(DATE_SUB(Expiry_date, INTERVAL p.days_remove_before_expiry_date DAY)) <= CURDATE()
            ORDER BY 
                i.product_id ASC,
                i.lot_id DESC";

        $data = Yii::app()->rodb->createCommand($sql)->queryAll();
        $data_to_return = [];

        $current_product_id = 0;
        $previous_row = [];
        $remaining_qty_sold_today = 0;

        foreach ($data as $key => &$row) {

            if($current_product_id == 0 || $current_product_id != $row['PRODUCT ID']) {
                $current_product_id = $row['PRODUCT ID'];
                $remaining_qty_sold_today = $row['QTY SOLD TODAY'];
                $qty_left_in_lot = ($row['QTY IN LOT'] - $row['QTY SOLD TODAY']) > 0 ? ($row['QTY IN LOT'] - $row['QTY SOLD TODAY']) : 0;
                $row['QTY WE CAN GIVE FOR FREE'] = $qty_left_in_lot;
                $row['LOSSES BASES ON RETAIL PRICE BY DEFAULT'] = $qty_left_in_lot*$row['PRODUCT RETAIL PRICE BY DEFAULT'];
                $remaining_qty_sold_today = ($remaining_qty_sold_today - $row['QTY IN LOT']) > 0 ? $remaining_qty_sold_today - $row['QTY IN LOT']: 0;
            } else {
                $qty_left_in_lot = ($row['QTY IN LOT'] - $remaining_qty_sold_today) > 0 ? ($row['QTY IN LOT'] - $remaining_qty_sold_today) : 0;
                $row['QTY WE CAN GIVE FOR FREE'] = $qty_left_in_lot;
                $row['LOSSES BASES ON RETAIL PRICE BY DEFAULT'] = $qty_left_in_lot*$row['PRODUCT RETAIL PRICE BY DEFAULT'];
                $remaining_qty_sold_today = ($remaining_qty_sold_today - $row['QTY IN LOT']) > 0 ? $remaining_qty_sold_today - $row['QTY IN LOT']: 0;
            }

            $data_to_return[] = $row;            
        }

        return $data_to_return;

    }

    public static function fullRouteSteps($params)
    {
        if (empty($params['end'])) {
            throw new Exception('Missing date value');
        }

        $sql = "SELECT
                    /*+ MAX_EXECUTION_TIME(900000) */ wwo.route_id AS 'Internal Route ID',
                    wws.order AS 'Sequence',
                    wws.address AS 'Address',
                    uhd.apt AS 'Apt Number',
                    IF(wwo.drop_instance_id IS NULL,'HD',drop_instance_type.name) AS 'Delivery Type',
                    companies.name as 'Contractor',
                    IF(wwo.drop_instance_id IS NULL,CONCAT(customer.first_name,' ',customer.last_name),droppoints.name) as 'Customer',
                    TIME_FORMAT(IF(wwo.drop_instance_id IS NULL,CONCAT(hd_di.opening_time),drop_instance.opening_time),'%H:%i') as 'Min Delivery Window',
                    TIME_FORMAT(IF(wwo.drop_instance_id IS NULL,CONCAT(hd_di.closing_time),drop_instance.closing_time),'%H:%i') as 'Max Delivery Window',
                    IF(wwo.drop_instance_id IS NULL,IF(o.gift_receiver_id IS NULL,o.number_box_needed,0),di_orders.box_needed) as 'Boxes',
                    IF(wwo.drop_instance_id IS NULL,IF(o.gift_receiver_id IS NULL,0,o.number_box_needed),di_orders.box_needed_gift) as 'Gift Boxes',
                    DATE_FORMAT(wws.scheduled_datetime,'%H:%i') as 'Scheduled Time',
                    DATE_FORMAT(wws.departed_datetime,'%H:%i') as 'Departed Time',
                    wws.scheduled_datetime as 'Scheduled DateTime',
                    DATE_FORMAT(SEC_TO_TIME(wws.end_sec - wws.start_sec),'%H:%i') as 'Service Time',
                    ROUND(wws.distance_to_next/1000,2) as Distance,
                    IF(wwo.drop_instance_id IS NULL, uhd.latitude, droppoints.lat) as 'Lat',
                    IF(wwo.drop_instance_id IS NULL, uhd.longitude, droppoints.lng) as 'Lng'
                FROM ww_orders wwo
                LEFT JOIN ww_steps wws
                    ON wws.order_id = wwo.order_id
                LEFT JOIN users driver
                    ON driver.user_id = wws.user_id
                LEFT JOIN user_comms driver_comms
                    ON driver_comms.user_id = driver.user_id AND driver_comms.main = 1
                LEFT JOIN ww_routes wwr 
                    ON (wwo.route_id = wwr.route_id)
                LEFT JOIN ww_vehicles veh 
                    ON (veh.vehicle_id = wwr.vehicle_id)
                LEFT JOIN companies
                    ON companies.company_id = veh.company_id OR (veh.company_id IS NULL AND companies.company_id = driver.company_id)
                LEFT JOIN orders o
                    ON o.order_id = wwo.order_number
                LEFT JOIN users customer
                    ON customer.user_id = o.user_id
                LEFT JOIN user_home_deliveries uhd
                    ON uhd.order_id = o.order_id
                LEFT JOIN drop_instance hd_di 
                    ON (hd_di.drop_instance_id = o.drop_instance_id)
                LEFT JOIN drop_instance 
                    ON (drop_instance.drop_instance_id = wwo.drop_instance_id)
                LEFT JOIN drop_instance_type
                    ON (drop_instance_type.drop_instance_type_id = drop_instance.type)
                LEFT JOIN droppoints 
                    ON drop_instance.droppoint_id = droppoints.droppoint_id 
                LEFT JOIN (
                        SELECT 
                            drop_instance_id, 
                            COUNT(orders.order_id) AS nb_orders,
                            SUM(IF(gift_receiver_id IS NULL, number_box_needed, 0)) AS box_needed,
                            SUM(IF(gift_receiver_id IS NOT NULL, number_box_needed, 0)) AS box_needed_gift
                        FROM 
                            orders
                        WHERE
                            status = 4 AND delivery_date = :date
                        GROUP BY 
                            drop_instance_id
                    ) di_orders 
                    ON drop_instance.drop_instance_id = di_orders.drop_instance_id
                LEFT JOIN 
                    (SELECT 
                        ww_step_id, 
                        COUNT(*) as violation_count 
                    FROM violations 
                    WHERE 
                        violation_date = :date 
                    GROUP BY ww_step_id) v 
                    ON v.ww_step_id = wws.step_id
                LEFT JOIN load_basket_route_info lbri ON (lbri.ww_route_id = wwr.route_id AND lbri.date = :date AND lbri.wave = 1)
                LEFT JOIN (
                    SELECT 
                        lbri_id,
                        SUM(IF(task.status='Done', 1, 0)) AS nb_loaded,
                        IFNULL(COUNT(task.task_id),0) AS nb_to_load,
                        DATE_FORMAT(MAX(task.finished_at), '%H:%i') AS loading_time
                    FROM 
                        task 
                    INNER JOIN load_basket_route_info lbri ON (lbri.date = :date AND lbri.wave = 1)
                    INNER JOIN `task_type_loadbasket` ON (task_type_loadbasket.`lbri_id` = lbri.route_info_id AND task_type_loadbasket.task_id = task.task_id)
                    GROUP BY 
                        lbri_id
                ) loading_status ON (loading_status.lbri_id = lbri.route_info_id)
                WHERE 
                    wwo.route_id IN (
                        SELECT
                            wwr.route_id
                        FROM 
                            ww_routes wwr
                        INNER JOIN
                            load_basket_route_info lbri ON (lbri.ww_route_id = wwr.route_id AND lbri.date = :date AND lbri.wave = 1)
                        INNER JOIN
                            ww_vehicles wwv ON wwv.vehicle_id = wwr.vehicle_id
                        WHERE wwr.date = :date
                        ORDER BY lbri.departure_time ASC, wwr.route_id ASC
                    )
                ORDER BY wwo.route_id, wws.order ASC, wws.arrival_sec ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':date', $params['end'])->queryAll();
    }

    public static function packerFluff($params)
    {
        $condition = '';
        $users_join = '';

        $cond_select = $params['default_group_by'] == 'Individual Entry' ? ", t.finished_at AS 'Task Completed At', p.created_at AS 'Labels Printed At'" : '';
        if (!empty($params['employee_ids'])) {
            $ids_string = self::getIdStringFromSelect($params['employee_ids']);
            $condition = " AND u.user_id IN ({$ids_string})";
            $users_join = ' LEFT JOIN users u ON u.user_id = t.assigned_to ';
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ t.task_id,
                u.user_id, 
                CONCAT(u.first_name, ' ', u.last_name) AS 'Employee Assigned',
                u.internal_employee_id AS 'EmployerD ID', 
                u.user_email AS 'Email'
                {$cond_select}
            FROM
                task t 
            RIGHT JOIN 
                task_type_prepbasket ttpb ON t.task_id = ttpb.task_id
            LEFT JOIN
                users u ON u.user_id = t.assigned_to
            LEFT JOIN
                task_type_label_printing p ON p.task_id = t.task_id
            WHERE
                date(t.finished_at) BETWEEN :start AND :end
                {$condition}
            ";
        $task_data = Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
        $ttpb_data = AppGlobal::arrayReduceWithKey($task_data, ['task_id']);
        $user_data = AppGlobal::arrayReduceWithKey($task_data, ['user_id']);
        $task_ids = [];

        if ($params['default_group_by'] == 'Individual Entry') {
            foreach (array_keys($ttpb_data) as $index => $t_id) {
                $sql_for_put_durations =
                    "SELECT
                        puts.task_id,
                        puts.updated_at,
                        unix_timestamp(puts.updated_at) - unix_timestamp(LAG(puts.updated_at) OVER(ORDER BY puts.updated_at)) AS put_length
                    FROM 
                        task_type_prepbasket puts
                    LEFT JOIN 
                        task t ON puts.task_id = t.task_id
                    {$users_join}
                    WHERE puts.task_id = {$t_id}
                    AND puts.status = 1
                    {$condition}
                    ORDER BY puts.updated_at ASC";

                $query_data[$t_id] = Yii::app()->rodb->createCommand($sql_for_put_durations)->queryAll();
                $task_ids[$t_id] = [
                    'Avg. s/put' => 0,
                    '4th to last put' => $query_data[$t_id][count($query_data[$t_id]) - 4]['updated_at'],
                    'Length of 4th to last put' => $query_data[$t_id][count($query_data[$t_id]) - 4]['put_length'],
                    '3rd to last put' => $query_data[$t_id][count($query_data[$t_id]) - 3]['updated_at'],
                    'Length of 3rd to last put' => $query_data[$t_id][count($query_data[$t_id]) - 3]['put_length'],
                    '2nd to last put' => $query_data[$t_id][count($query_data[$t_id]) - 2]['updated_at'],
                    'Length of 2nd to last put' => $query_data[$t_id][count($query_data[$t_id]) - 2]['put_length'],
                    'Last put' => $query_data[$t_id][count($query_data[$t_id]) - 1]['updated_at'],
                    'Length of last put' => $query_data[$t_id][count($query_data[$t_id]) - 1]['put_length'],
                    'Length from last put to labels' => $ttpb_data[$t_id]['Labels Printed At'] != null ? strtotime($ttpb_data[$t_id]['Labels Printed At']) - strtotime($query_data[$t_id][count($query_data[$t_id]) - 1]['updated_at']) : null,
                    'Length from labels to task completion' => $ttpb_data[$t_id]['Labels Printed At'] != null ? strtotime($ttpb_data[$t_id]['Task Completed At']) - strtotime($ttpb_data[$t_id]['Labels Printed At']) : null,
                    'Extra put info for task' => 'https://montreal.lufa.com/en/paymentLogging/ViewEmployeeTask/task_id/'.$t_id,
                ];
                foreach ($query_data[$t_id] as $put_data_row) {
                    $task_ids[$t_id]['Avg. s/put'] += $put_data_row['put_length'];
                }
                $task_ids[$t_id]['Avg. s/put'] = round($task_ids[$t_id]['Avg. s/put'] / count($query_data[$t_id]));
                $task_ids[$index] = array_merge($ttpb_data[$t_id], $task_ids[$t_id]);
                unset($task_ids[$t_id], $task_ids[$index]['task_id'], $task_ids[$index]['user_id']);
            }
        } else {
            $index = 0;
            foreach (array_keys($user_data) as $u_id) {
                $tasks = implode(', ', array_keys($ttpb_data));
                $group_by = self::getGroupByForQuery('default_group_by', $params['default_group_by'], 't.finished_at');
                $sql_for_put_durations =
                    "SELECT
                        puts.task_id,
                        puts.updated_at,
                        unix_timestamp(puts.updated_at) - unix_timestamp(LAG(puts.updated_at) OVER(ORDER BY puts.updated_at)) AS put_length,
                        {$group_by} AS 'Group Time',
                        t.assigned_to,
                        p.created_at AS 'Labels Printed At',
                        t.finished_at
                    FROM 
                        task_type_prepbasket puts
                    LEFT JOIN 
                        task t ON t.task_id = puts.task_id
                    LEFT JOIN
                       task_type_label_printing p ON p.task_id = t.task_id
                    {$users_join}
                    WHERE 
                        t.assigned_to = {$u_id}
                    AND
                        puts.task_id IN({$tasks})
                    AND puts.status = 1
                    {$condition}
                    ORDER BY puts.updated_at ASC";

                $put_data = Yii::app()->rodb->createCommand($sql_for_put_durations)->queryAll();
                $group_time = AppGlobal::arrayReduceWithKey($put_data, ['Group Time', 'task_id'], false);
                foreach ($group_time as $grp_time => $tasks_arr) {
                    $task_ids[$index] = [
                        'Employee Assigned' => $user_data[$u_id]['Employee Assigned'],
                        'EmployerD ID' => $user_data[$u_id]['EmployerD ID'],
                        'Email' => $user_data[$u_id]['Email'],
                        'Group Time' => $grp_time,
                        'Avg. s/put' => 0,
                        'Avg. length of 4th to last put' => 0,
                        'Avg. length of 3rd to last put' => 0,
                        'Avg. length of 2nd to last put' => 0,
                        'Avg. length of last put' => 0,
                        'Avg. length from last put to labels' => 0,
                        'Avg. length from labels to task completion' => 0,
                    ];

                    foreach ($tasks_arr as $t_id => $puts_datum_arr) {
                        $total_put_time = 0;
                        foreach ($puts_datum_arr as $i => $put) {
                            // first put length needs to be 0
                            if ($i != 0) {
                                $total_put_time += $put['put_length'];
                            }
                        }
                        $task_ids[$index]['Avg. s/put'] += $total_put_time / count($puts_datum_arr);
                        $task_ids[$index]['Avg. length of 4th to last put'] += intval($puts_datum_arr[count($puts_datum_arr) - 4]['put_length']);
                        $task_ids[$index]['Avg. length of 3rd to last put'] += intval($puts_datum_arr[count($puts_datum_arr) - 3]['put_length']);
                        $task_ids[$index]['Avg. length of 2nd to last put'] += intval($puts_datum_arr[count($puts_datum_arr) - 2]['put_length']);
                        $task_ids[$index]['Avg. length of last put'] += intval($puts_datum_arr[count($puts_datum_arr) - 1]['put_length']);
                        if ($puts_datum_arr[0]['Labels Printed At'] == null) {
                            $task_ids[$index]['Avg. length from last put to labels'] = null;
                            $task_ids[$index]['Avg. length from labels to task completion'] = null;
                        } else {
                            $task_ids[$index]['Avg. length from last put to labels'] += strtotime(intval($puts_datum_arr[0]['Labels Printed At'])) - intval($puts_datum_arr[count($puts_datum_arr) - 1]['updated_at']);
                            $task_ids[$index]['Avg. length from labels to task completion'] += intval($puts_datum_arr[0]['finished_at']) - strtotime(intval($puts_datum_arr[0]['Labels Printed At']));
                        }
                    }
                    $task_ids[$index]['Avg. s/put'] = round($task_ids[$index]['Avg. s/put'] / count($tasks_arr));
                    $task_ids[$index]['Avg. length of 4th to last put'] = round($task_ids[$index]['Avg. length of 4th to last put'] / count($tasks_arr));
                    $task_ids[$index]['Avg. length of 3rd to last put'] = round($task_ids[$index]['Avg. length of 3rd to last put'] / count($tasks_arr));
                    $task_ids[$index]['Avg. length of 2nd to last put'] = round($task_ids[$index]['Avg. length of 2nd to last put'] / count($tasks_arr));
                    $task_ids[$index]['Avg. length of last put'] = round($task_ids[$index]['Avg. length of last put'] / count($tasks_arr));
                    if ($task_ids[$index]['Avg. length from last put to labels'] == null) {
                        $task_ids[$index]['Avg. length from last put to labels'] = null;
                        $task_ids[$index]['Avg. length from labels to task completion'] = null;
                    } else {
                        $task_ids[$index]['Avg. length from last put to labels'] = round($task_ids[$index]['Avg. length from last put to labels'] / count($tasks_arr));
                        $task_ids[$index]['Avg. length from labels to task completion'] = round($task_ids[$index]['Avg. length from labels to task completion'] / count($tasks_arr));
                    }
                    $index++;
                }
            }
        }

        return $task_ids;
    }

    public static function donationProgramStatisticsAllTime($params)
    {
        $sql = "
        SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ u.user_id AS 'User ID',
            CONCAT(u.first_name, ' ', u.last_name) AS 'Name',
            c.name AS 'Charity',
            u.family_size AS 'Family Size',
            IF(s.active = 1, 'Active', 'Inactive') AS 'Subscription Status',
            u.created AS 'Account created on',
            order_stats.nb_shipped AS 'Number of orders taken',
            order_stats.nb_generated AS 'Number of orders genrated',
            ROUND((order_stats.nb_shipped/order_stats.nb_generated*100),2) AS 'Take rate (%)',
            order_stats.first_charity_order AS 'Part of program since',
            order_stats.last_charity_order'last order date being part of program',
            ROUND(order_stats.money_received,2) AS 'Total credits used in $ (from the Lufavores)',
            ROUND(order_stats.total_amount_purchased-order_stats.money_received,2) AS 'Total amount paid with own money in $',
            order_stats.total_discount AS 'Discount on fruits/vegetables in $'
        FROM 
            users u
        LEFT JOIN companies c ON (c.company_id = u.company_id)
        LEFT JOIN subscriptions s ON (s.user_id = u.user_id)
        LEFT JOIN (
            SELECT 
                o.user_id,
                SUM(IF(o.status = 4, 1, 0)) as nb_shipped,
                COUNT(o.order_id) as nb_generated,
                MIN(CASE WHEN o.status = 4 AND charity_received=0 THEN o.delivery_date END) AS first_order_as_regular_customer,
                MIN(CASE WHEN o.status = 4 AND charity_received>0 THEN o.delivery_date END) AS first_charity_order,
                MAX(CASE WHEN o.status = 4 AND charity_received>0 THEN o.delivery_date END) AS last_charity_order,
                SUM(IF(o.charity_received>0, o.charity_received, 0)) AS money_received,
                SUM(o.total_order_amount) AS total_amount_purchased,
                details.total_discount
            FROM 
                orders o
            LEFT JOIN (
                SELECT
                    orders.user_id,
                    SUM(IF(order_details.paid_price_per_unit_for_default_weight<order_details.default_retail_price_per_unit, (order_details.default_retail_price_per_unit-order_details.paid_price_per_unit_for_default_weight), 0)) AS total_discount
                FROM
                    orders
                INNER JOIN order_details ON (orders.order_id = order_details.order_id)
                WHERE 
                    (orders.user_id IN (SELECT user_id FROM users WHERE charitable_account = 1) OR orders.user_id IN (SELECT DISTINCT user_id FROM orders WHERE orders.company_id_associated IS NOT NULL)) AND 
                    orders.status = 4 AND 
                    orders.company_id_associated IS NOT NULL
                GROUP BY 
                    orders.user_id
            ) details ON (details.user_id = o.user_id)
            WHERE 
                (o.user_id IN (SELECT user_id FROM users WHERE charitable_account = 1) OR o.user_id IN (SELECT DISTINCT user_id FROM orders WHERE orders.company_id_associated IS NOT NULL))
            GROUP BY 
                o.user_id
        ) order_stats ON (order_stats.user_id = u.user_id)
        WHERE 
            (u.charitable_account = 1 OR u.user_id IN (SELECT DISTINCT user_id FROM orders WHERE orders.company_id_associated IS NOT NULL))";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function donationProgramStatisticsForSpecificPeriod($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "
        SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ u.user_id AS 'User ID',
            CONCAT(u.first_name, ' ', u.last_name) AS 'Name',
            c.name AS 'Charity',
            order_stats.nb_shipped AS 'Number of orders taken',
            order_stats.nb_generated AS 'Number of orders genrated',
            ROUND((order_stats.nb_shipped/order_stats.nb_generated*100),2) AS 'Take rate (%)',
            ROUND(order_stats.money_received,2) AS 'Total credits used in $ (from the Lufavores)',
            ROUND(order_stats.total_amount_purchased-order_stats.money_received,2) AS 'Total amount paid with own money in $',
            order_stats.total_discount AS 'Discount on fruits/vegetables in $'
        FROM 
            users u
        LEFT JOIN companies c ON (c.company_id = u.company_id)
        LEFT JOIN subscriptions s ON (s.user_id = u.user_id)
        LEFT JOIN (
            SELECT 
                o.user_id,
                SUM(IF(o.status = 4, 1, 0)) as nb_shipped,
                COUNT(o.order_id) as nb_generated,
                SUM(IF(o.charity_received>0, o.charity_received, 0)) AS money_received,
                SUM(o.total_order_amount) AS total_amount_purchased,
                details.total_discount
            FROM 
                orders o
            LEFT JOIN (
                SELECT
                    orders.user_id,
                    SUM(IF(order_details.paid_price_per_unit_for_default_weight<order_details.default_retail_price_per_unit, (order_details.default_retail_price_per_unit-order_details.paid_price_per_unit_for_default_weight), 0)) AS total_discount
                FROM
                    orders
                INNER JOIN order_details ON (orders.order_id = order_details.order_id)
                WHERE 
                    (orders.user_id IN (SELECT user_id FROM users WHERE charitable_account = 1) OR orders.user_id IN (SELECT DISTINCT user_id FROM orders WHERE orders.company_id_associated IS NOT NULL)) AND 
                    orders.status = 4 AND 
                    orders.company_id_associated IS NOT NULL AND 
                    orders.delivery_date BETWEEN :start AND :end
                GROUP BY 
                    orders.user_id
            ) details ON (details.user_id = o.user_id)
            WHERE 
                (o.user_id IN (SELECT user_id FROM users WHERE charitable_account = 1) OR o.user_id IN (SELECT DISTINCT user_id FROM orders WHERE orders.company_id_associated IS NOT NULL)) AND 
                o.delivery_date BETWEEN :start AND :end
            GROUP BY 
                o.user_id
        ) order_stats ON (order_stats.user_id = u.user_id)
        WHERE 
            (u.charitable_account = 1 OR u.user_id IN (SELECT DISTINCT user_id FROM orders WHERE orders.company_id_associated IS NOT NULL AND orders.status = 4))";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function giftedCreditsForSpecificPeriod($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ DATE(created) 'date',
            COUNT(*) 'number of gift certificates',
            SUM(amount) 'total gifted'
        FROM 
            coupons
        WHERE 
            gift_certificate = 1 AND 
            DATE(created) BETWEEN :start AND :end
        GROUP BY 
            DATE(created)
        ORDER BY 
            DATE(created) ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function receptionErrors($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ t.supplier_forecast_orders_id AS 'SFID ID',
                suppliers.name AS 'Supplier Name', 
                products.name AS 'Product Name', 
                DATE(t.reception_timestamp) AS 'Reception Date', 
                t.shipping_type AS 'Shipping Method', 
                re.creator_id AS 'Revieved By',
                re.type AS 'Error Type',
                re.comment AS 'Error Comment',
                IF(re.status = 1, 'Need to investigate further', IF(re.status = 2, 'Affects reception bonus', 'Status not set')) AS 'Error Status'
            FROM 
                supplier_forecast_orders t
            LEFT JOIN products ON products.product_id = t.product_id
            LEFT JOIN suppliers ON products.supplier_id = suppliers.supplier_id
            LEFT JOIN supplier_forecast_order_reception_errors re ON t.supplier_forecast_orders_id = re.supplier_forecast_orders_id
            WHERE 
                re.type IS NOT NULL AND 
                DATE(t.reception_timestamp) BETWEEN :start AND :end;
            ";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function emailsOfActiveLufavores($params)
    {
        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ DISTINCT uc.email, 
                uc.name 
            FROM 
                user_comms uc 
            INNER JOIN subscriptions s ON (s.user_id = uc.user_id AND s.active = 1) 
            WHERE 
                uc.in_use = 1 
            GROUP BY uc.email;';

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function ageOfInventory($params, $productIds = [])
    {
        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ tmp.lot_id AS "LOT ID", 
                tmp.display_location AS "STORAGE LOCATION",
                tmp.conservation_mode_in_warehouse AS "CONSERVATION MODE",
                IF(tmp.flow_through = 1, "Yes", "No") AS "FLOW-THROUGH ?",
                tmp.product_id AS "PRODUCT ID", 
                tmp.catname as "CATEGORY NAME",
                tmp.pname AS "PRODUCT NAME", 
                tmp.sname AS "SUPPLIER NAME",
                tmp.purchaser AS "PURCHASER",
                tmp.category_manager AS "CATEGORY MANAGER", 
                tmp.number_of_units_available AS "QTY IN INVENTORY", 
                tmp.volume*tmp.number_of_units_available AS "VOLUME OF INVENTORY",
                tmp.Expiry_date AS "EXPIRATION DATE",
                tmp.days_remove_before_expiry_date AS "NB DAYS BEFORE THE EXPIRATION OF A PRODUCT IT SHOULD BE REMOVED FROM SALE", 
                SUBDATE(tmp.Expiry_date, INTERVAL tmp.days_remove_before_expiry_date DAY) AS "SHOULD BE REMOVED FROM SALE ON",
                tmp.created AS "LOT CREATED ON",
                CASE
                    WHEN nb_days_in_inventory BETWEEN 0 AND 3 THEN "<= 3 days old" 
                    WHEN nb_days_in_inventory BETWEEN 4 AND 7 THEN "4-7 days old"
                    WHEN nb_days_in_inventory BETWEEN 8 AND 30 THEN "8-30 days old"
                    WHEN nb_days_in_inventory BETWEEN 31 AND 90 THEN "31-90 days old"
                    WHEN nb_days_in_inventory BETWEEN 91 AND 180 THEN "91-180 days old"
                    WHEN nb_days_in_inventory BETWEEN 181 AND 360 THEN "181-360 days old"
                    ELSE "361+ days old"
                END AS "AGE OF LOT",
                ROUND(tmp.number_of_units_available*tmp.pprice) AS "COST OF CURRENT QTY IN"
            FROM (
                SELECT 
                    i.`lot_id`, 
                    i.`product_id`, 
            pc.name as "catname",
                    p.name AS pname, 
                    p.days_remove_before_expiry_date,
                    s.name AS sname, 
                    i.`number_of_units_available`, 
                    i.`Expiry_date`, 
                    i.created,
                    DATEDIFF(CURDATE(), i.created) AS nb_days_in_inventory,
                    IFNULL(sfo.price_per_unit, 0) as pprice, 
                    lh.display_location,
                    p.conservation_mode_in_warehouse,
                    p.volume,
                    p.flow_through,
                    CONCAT(u.first_name, " ", u.last_name) AS "purchaser",
                    CONCAT(u1.first_name, " ", u1.last_name) AS "category_manager"
                FROM 
                    `inventory` i
                LEFT JOIN products p ON (i.product_id = p.product_id)
                LEFT JOIN productSubSubCategories pssc on (p.sub_sub_id = pssc.sub_sub_id)
                LEFT JOIN productSubCategories psc on (pssc.subcategory_id = psc.subcategory_id)
                LEFT JOIN product_categories pc on (psc.category_id = pc.category_id)
                LEFT JOIN suppliers s ON (s.supplier_id = p.supplier_id)
                LEFT JOIN supplier_forecast_orders sfo ON (sfo.supplier_forecast_orders_id = i.supplier_forecast_orders_id)
                LEFT JOIN location_header lh ON (lh.location_header_id = i.location_header_id)
                LEFT JOIN users u ON (u.user_id = p.purchaser_id)
                LEFT JOIN users u1 ON (u1.user_id = p.category_manager_id)
                WHERE 
                    i.`number_of_units_available` > 0';

        if (!empty($productIds)) {
            $sql .= ' AND p.product_id IN ('.implode(',', $productIds).')';
        }
                $sql .= ') tmp';

        $data = Yii::app()->rodb->createCommand($sql)->queryAll();
        $conservation_temp_map = Products::model()->loadInventoryTemperature();

        foreach ($data as $key => &$value) {
            $value['CONSERVATION MODE'] = $conservation_temp_map[$value['CONSERVATION MODE']];
        }

        return $data;
    }

    public static function productsToBeCut($params)
    {
        $productIds = [10150, 12040, 13092, 12183, 15057, 14387, 14789, 15054, 15062, 13233, 12039, 15066, 13713, 12142, 15063, 15048, 13749, 15060, 15050, 13401, 7792, 13804, 14055, 13751, 13399, 14054, 13377, 13526, 7091, 7799, 12832, 13400, 12715, 10337, 13810, 14925, 13402, 9860, 10849, 14912, 5219, 14056, 13172, 14051, 15224, 5212, 12121, 14256, 14531, 9765, 14879, 14878, 14908, 14658, 10860, 13530, 10858, 14714, 14611, 9220, 14529, 10578, 10856, 13623, 14801, 10251, 14851, 10859, 14262, 11853, 11352, 11659, 6845, 12611, 13079, 10857, 14257, 10120, 15059, 14260, 14853, 13706, 10843, 14008, 13617, 4264, 13916, 12612, 14965, 3737, 10759, 10853, 11023, 13080, 13082, 10032, 2675, 6359, 2676, 14429, 14644, 10153, 6335, 14992, 6317, 14856, 14854, 11277, 11586, 2679, 10127, 10792, 11789, 10782, 14873, 14795, 10511, 8713, 11284, 10164, 13871, 8785, 13217, 5997, 12501, 11567, 14261, 447, 13273, 8918, 12853, 10680, 2678, 14645, 14365, 14970, 14002, 15091, 6889, 14907, 14005, 14235, 14004, 14942, 12830, 10129, 10643, 14911, 10525, 13702, 4188, 14977, 8983, 13710, 13086, 10062, 13595, 10644, 4369, 10558, 11565, 11285, 14627, 12833, 13693, 13746, 12994, 8919, 13864, 12839, 6375, 10531, 15228, 11131, 13747, 15141, 3696, 7157, 14960, 14433, 14976, 10390, 13697, 13419, 14350, 10392, 14870, 14807, 15031, 14754, 14834, 12827, 15205, 10678, 12037, 10645, 14753, 10425, 12870, 10679, 6856, 10126, 12837, 12042, 14344, 13098, 12553, 8688, 6991, 15150, 15140, 14217, 6855, 6983, 14814, 14538, 14234, 15074, 14432, 14775, 14752, 15070, 14028, 14161, 11456, 13525, 14971, 14343, 8201, 14078, 15149];

        return static::ageOfInventory($params, $productIds);
    }

    public static function numberOfShorts($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ CONCAT(u.first_name, ' ', u.last_name) AS 'Purchaser',
                CONCAT(u1.first_name, ' ', u1.last_name) AS 'Category Manager',
                pc.name AS 'Category',
                p.product_id 'Product ID',
                p.name 'Product Name',
                s.name 'Supplier Name',
                IF(p.flow_through = 1, 'Y', 'N') 'Flow through (Y/N)',
                COUNT(od.order_details_id) 'Quantity Sold',
                SUM(IF(od.refunded_reason = 31, 1, 0)) 'Quantity Short',
                AVG(od.paid_price_per_unit_for_default_weight) 'Average retail product price',
                SUM(IF(od.refunded_reason = 31, od.paid_price_per_unit_for_default_weight, 0)) 'Value of shorts',
                IFNULL(recv.quantity, 0) 'Quantity received the same day',
                o.delivery_date 'Date'
            FROM 
                order_details od
            INNER JOIN orders o ON (o.order_id = od.order_id AND o.delivery_date BETWEEN :start AND :end AND o.status = 4)
            INNER JOIN products p ON p.product_id = od.product_id
            INNER JOIN productSubSubCategories AS pssc ON pssc.sub_sub_id = p.sub_sub_id
            INNER JOIN productSubCategories AS psc ON pssc.subcategory_id = psc.subcategory_id
            INNER JOIN product_categories AS pc ON psc.category_id = pc.category_id
            INNER JOIN suppliers s ON s.supplier_id = p.supplier_id
            LEFT JOIN users u ON u.user_id = p.purchaser_id
            LEFT JOIN users u1 ON u1.user_id = p.category_manager_id
            LEFT JOIN (
                SELECT
                    delivery_date,
                    product_id,
                    sum(quantity_accepted) quantity
                FROM 
                    supplier_forecast_orders
                WHERE
                    delivery_date BETWEEN :start AND :end AND 
                    status = 4
                GROUP BY
                    delivery_date, product_id
            ) recv ON (p.product_id = recv.product_id AND o.delivery_date = recv.delivery_date)
            GROUP BY
                o.delivery_date, od.product_id
            HAVING
                `Quantity Short` > 0
            ORDER BY
                o.delivery_date, s.supplier_id, od.product_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function salesFromDiscounts($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ w AS "WEEK",
                product_id AS "PRODUCT ID",
                product_name AS "PRODUCT NAME",
                categ AS "CATEGORY NAME",
                subcateg AS "SUBCATEGORY NAME",
                subsubcateg AS "SUB-SUBCATEGORY NAME",
                supplier_name AS "SUPPLIER NAME",
                qty_sold AS "QUANTITY SOLD",
                sales AS "REVENUE",
                cost AS "COST",
                margin AS "MARGIN",
                revenue_deals AS "REVENUE DEALS",
                cost_deals AS "COST DEALS",
                ROUND(100-((cost_deals/revenue_deals)*100),2) AS "MARGIN DEALS",
                ROUND((revenue_deals/sales)*100,2) AS "% REVENUE FROM DEALS"
            FROM (
                SELECT 
                    YEARWEEK(o.delivery_date, 6) AS w,
                    p.product_id AS product_id,
                    p.name AS product_name, 
                    pc.name AS categ,
                    psc.name AS subcateg,
                    pssc.name AS subsubcateg,
                    s.name AS supplier_name,
                    COUNT(od.order_details_id) AS qty_sold,
                    SUM(od.defined_retail_price_per_unit_for_default_weight) AS sales,
                    SUM(od.purchase_price_per_unit) AS cost,
                    ROUND(100-(SUM(od.purchase_price_per_unit)/SUM(od.defined_retail_price_per_unit_for_default_weight)*100),2) AS margin,
                    SUM(IF(od.defined_retail_price_per_unit_for_default_weight<od.default_retail_price_per_unit, od.defined_retail_price_per_unit_for_default_weight, 0)) AS revenue_deals,
                    SUM(IF(od.defined_retail_price_per_unit_for_default_weight<od.default_retail_price_per_unit, od.purchase_price_per_unit, 0)) AS cost_deals
                FROM 
                    orders o
                INNER JOIN order_details od ON (od.order_id = o.order_id)
                INNER JOIN products p ON (p.product_id = od.product_id)
                INNER JOIN productSubSubCategories pssc ON (pssc.sub_sub_id = p.sub_sub_id)
                INNER JOIN productSubCategories psc ON (pssc.subcategory_id = psc.subcategory_id)
                INNER JOIN product_categories pc ON (pc.category_id = psc.category_id)
                INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
                WHERE
                    o.delivery_date BETWEEN :start AND :end AND o.status = 4 AND p.supplier_id NOT IN (1,113,376,597,922,935,972)
                GROUP BY 
                    YEARWEEK(o.delivery_date), p.product_id
            ) tmp 
            GROUP BY 
                tmp.w, 
                tmp.product_id';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function creditsAndRefundsWithRevertedTransactions($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ (CASE t.type 
                WHEN 31 THEN 'Shorts (31)'
                WHEN 33 THEN 'Quality Complaints (33)'
                WHEN 34 THEN 'Missing Products (34)'
                WHEN 35 THEN 'Technical Issue (35)'
                WHEN 36 THEN 'Late Delivery Refunds (36)'
                WHEN 37 THEN 'Damaged Products (37)'
                WHEN 39 THEN 'Refunded of unwanted products (39)'
                WHEN 60 THEN 'Product didn\\'t meet quality standards (60)'
                WHEN 61 THEN 'Missing/Lost Box (61)' 
                WHEN 106 THEN 'HD delivery not done (106)'
                WHEN 80 THEN 'revert (80)'
                WHEN 45 THEN 'Packaging complaint (45)'
                END) 'refund reason',
                count(*) 'count',
                sum(t.amount) 'amount'
            FROM
                transactions t
            WHERE
                DATE(t.created_time) BETWEEN :start AND :end AND
                t.type IN (31, 33, 34, 35, 36, 37, 39, 60, 61, 106, 45,80)
            GROUP BY
                t.type";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function remainingTicketsPerCommunityGroup($params)
    {
        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ c.name, 
                count(*) AS '# tickets'
            FROM 
                tickets t 
            LEFT JOIN users u ON (t.requestor_id = u.user_id) 
            LEFT JOIN community_representative_group c ON (u.community_representative_group_id = c.community_representative_group_id)
            WHERE 
                t.status in (0,1) 
            GROUP BY 
                u.community_representative_group_id";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function refunds($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ YEARWEEK(t.created_time, 6) AS week_number,
                (case t.type 
                    when 33 then 'Quality Complaints (33)'
                    when 34 then 'Missing Products (34)'
                    when 35 then 'Technical Issue (35)'
                    when 36 then 'Late Delivery Refunds (36)'
                    when 37 then 'Damaged Products (37)'
                    when 39 then 'Refunded of unwanted products (39)'
                    when 60 then 'Product didn\\'t meet quality standards (60)'
                    when 61 then 'Missing/Lost Box (61)' 
                    when 106 then 'HD delivery not done (106)'
                    when 45 then 'Packaging complaint (45)'
                    when 82 then 'Refund of delivery box (82)'
                    when 43 then 'Customer retention (43)'
                    when 70 then 'Chargeback (70)'
                    when 72 then 'Bad debt due to chargeback (72)'
                    when 80 then 'Revert refund (80)'
                    else t.type
                end) 'refund reason',
                SUM(t.amount) AS tot
            FROM `transactions` t
            WHERE
                DATE(t.created_time) BETWEEN :start AND :end
                AND t.finalized = 1 
                AND (t.type NOT IN (11, 13, 16, 19, 20, 21, 24, 27, 28 ,31, 32, 38, 40, 41, 42, 55, 56, 69, 107) OR (t.type = 82 AND t.staff_id IS NOT NULL)) AND 
                t.child_transaction_id <= 0 
            GROUP BY
                YEARWEEK(t.created_time, 6), t.type";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function linesPerHour($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ date as 'Date',
                ROUND(3600/(((SUM(TIME_TO_SEC(TIMEDIFF(finished_at,started_at))) + (SELECT 
                    SUM(t_time) 
                FROM (SELECT 
                        TIME_TO_SEC(TIMEDIFF(MAX(IF(t.status != 'Done',ttp.updated_at,t.finished_at)),t.started_at)) as t_time
                    FROM task t 
                    INNER JOIN 
                        task_type_prepbaskethelper ttph 
                    ON ttph.task_id = t.task_id 
                    LEFT JOIN task_type_prepbasket ttp 
                    ON ttp.task_id = ttph.helping_task_id AND ttp.reserved_by = t.assigned_to
                WHERE 
                    DATE(t.started_at) = task_grouped.date
                GROUP BY t.task_id) as help)))/SUM(puts_done))) as 'Lines/h'
            FROM (SELECT
                    DATE(started_at) as date,
                    SUM(IF(ttp.status = 1,1,0)) as puts_done,
                    MAX(IF(t.status != 'Done',ttp.updated_at,t.finished_at)) as finished_at,
                    started_at
                FROM task_type_prepbasket ttp 
                LEFT JOIN task t 
                ON t.task_id = ttp.task_id
                WHERE DATE(t.started_at) BETWEEN :start AND :end
                GROUP BY 
                    t.task_id) as task_grouped
            GROUP BY date";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function loadingTimeReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        return Yii::app()->db->createCommand(
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(t.finished_at) as Date,
                c.serial as 'Cart ID',
                wwv.external_id as 'Route',
                lbri.departure_time as 'Scheduled Loading',
                parent.finished_at as 'Packing Task Completed',
                t.finished_at as 'Loading Task Completed',
                ROUND((unix_timestamp(t.finished_at) - unix_timestamp(parent.finished_at))/3600,2) AS 'Time Between Packing and Loading (h)',
                unix_timestamp(t.finished_at) - unix_timestamp(LAG(t.finished_at) OVER (PARTITION BY Date(t.finished_at) ORDER BY t.finished_at ASC)) as 'Time Since Last Load (s)'
            FROM task t 
            INNER JOIN task_type_loadbasket ttl
                ON ttl.task_id = t.task_id
            INNER JOIN task_parent_child tpc
                ON tpc.child_id = t.task_id
            INNER JOIN task parent
                ON parent.task_id = tpc.parent_id AND parent.task_type_defaults_id = :packing
            LEFT JOIN load_basket_route_info lbri
                ON lbri.route_info_id = ttl.lbri_id
            LEFT JOIN cart_activity ca
                ON ca.task_id = t.task_id AND ca.status = :cart_loaded
            LEFT JOIN cart c
                ON c.cart_id = ca.cart_id
            LEFT JOIN ww_routes wwr
                ON wwr.route_id = lbri.ww_route_id
            LEFT JOIN ww_vehicles wwv
                ON wwv.vehicle_id = wwr.vehicle_id
            WHERE DATE(t.finished_at) BETWEEN :start AND :end
            ORDER BY t.finished_at ASC
            "
        )->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':cart_loaded' => CartActivity::STATUS_LOADED,
            ':packing' => TaskTypeDefaults::PrepOrder,
        ])->queryAll();
    }

    public static function statsReminderToActivate($params)
    {
        return Yii::app()->db->createCommand(
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ 'Activation Reminder',
                send_date AS 'Reminder sent on',
                total_sent AS 'Number of emails sent',
                COUNT(email_schedule_id) AS 'Number of unique opens',
                SUM(IF(order_id IS NOT NULL, 1, 0)) AS 'Number of CX who got a basket'
            FROM (
                SELECT 
                    es.email_schedule_id,
                    es.send_date,
                    es.total_sent,
                    stats.user_id,
                    stats.created_at,
                    o.order_id,
                    o.status, 
                    o.delivery_date
                FROM 
                    `email_schedule` es
                INNER JOIN securelufacom.email_stats stats ON (stats.email_schedule_id = es.email_schedule_id AND stats.type=1)
                LEFT JOIN orders o ON (o.delivery_date = DATE_ADD(es.send_date, INTERVAL 1 DAY) AND o.user_id = stats.user_id AND o.status = 4)
                WHERE 
                    es.`email_campaign_id` = 8 AND 
                    es.schedule_type = 6 AND 
                    es.send_date BETWEEN SUBDATE(CURDATE(), INTERVAL 3 WEEK) AND CURDATE()
                GROUP BY 
                    es.email_schedule_id,
                    stats.user_id
            ) tmpq
            GROUP BY 
                email_schedule_id
            "
        )->queryAll();
    }

    public static function statsReminderToCustomize($params)
    {
        return Yii::app()->db->createCommand(
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ 'Customization Reminder',
                send_date AS 'Reminder sent on',
                total_sent AS 'Number of emails sent',
                COUNT(email_schedule_id) AS 'Number of unique opens',
                SUM(IF(cust_id IS NOT NULL, 1, 0)) AS 'Number of CX who customized'
            FROM (
                SELECT
                    es.email_schedule_id,
                    es.send_date,
                    es.total_sent,
                    stats.user_id,
                    stats.created_at,
                    cl.cust_id
                FROM
                    `email_schedule` es
                INNER JOIN securelufacom.email_stats stats ON (stats.email_schedule_id = es.email_schedule_id AND stats.type=1)
                LEFT JOIN orders o ON (o.delivery_date = DATE_ADD(es.send_date, INTERVAL 1 DAY) AND o.user_id = stats.user_id AND o.status = 4)
                LEFT JOIN securelufacom.customization_log cl ON (o.order_id = cl.order_id AND cl.date = es.send_date AND cl.time>=DATE_FORMAT(stats.created_at, '%H:%i:%s') AND cl.user_id = stats.user_id)
                WHERE
                    es.`email_campaign_id` = 8 AND
                    es.schedule_type = 5 AND
                    es.send_date BETWEEN SUBDATE(CURDATE(), INTERVAL 3 WEEK) AND CURDATE()
                GROUP BY
                    es.email_schedule_id,
                    stats.user_id
            ) tmpq
            GROUP BY
                email_schedule_id
            "
        )->queryAll();
    }

    public static function receptionPutaways($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(sfid.reception_timestamp) 'date',
                CONCAT(ur.first_name, ' ', ur.last_name) 'received by',
                sfid.reception_timestamp 'received at',
                p.product_id 'product id',
                p.name 'product name',
                s.name 'supplier name',
                IF(p.flow_through = 1, 'Y', 'N') 'flow through (Y/N)',
                IF(tti.task_type_inventorycheck_id is not null, 'Y', 'N') 'flagged issue for not found (Y/N)',
                IF(ui.user_id is not null, concat(ui.first_name, ' ', ui.last_name), '') 'checked by',
                lh.display_location 'putaway location'
            FROM 
                supplier_forecast_orders sfid
            INNER JOIN products p ON (p.product_id = sfid.product_id)
            INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
            INNER JOIN users ur ON (sfid.reception_done_by = ur.user_id)
            LEFT JOIN task_type_inventorycheck tti ON (tti.lot_id = sfid.lot_id AND tti.reason_id = 1 AND DATE(tti.created_date) = date(sfid.reception_timestamp))
            LEFT JOIN task t ON (tti.task_id = t.task_id)
            LEFT JOIN users ui ON (ui.user_id = t.assigned_to)
            LEFT JOIN inventory i ON (i.lot_id = sfid.lot_id)
            LEFT JOIN location_header lh ON (i.location_header_id = lh.location_header_id)
            WHERE
                sfid.status = 4 AND 
                DATE(sfid.reception_timestamp) BETWEEN :start AND :end
            ORDER BY
                sfid.reception_timestamp, tti.task_type_inventorycheck_id, p.product_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function receptionWeights($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ s.reception_timestamp AS 'Reception date',
                s.supplier_forecast_orders_id AS 'SFID',
                s.product_id AS 'Product ID',
                p.name AS 'Product Name',
                ROUND(p.weight/1000, 3) AS 'Weight from product page in kg)',
                s.weight_sample_1 AS 'Weight Sample 1 (kg)',
                s.weight_sample_2 AS 'Weight Sample 2 (kg)',
                s.weight_sample_3 AS 'Weight Sample 3 (kg)',
                CONCAT(u.first_name, ' ', u.last_name) AS 'Received by'
            FROM
                `supplier_forecast_orders` s
            INNER JOIN products p ON (p.product_id = s.product_id AND p.weight_published_and_always_same = 0)
            LEFT JOIN users u ON (u.user_id = s.reception_done_by)
            WHERE
                s.weight_sample_1 = s.weight_sample_2 AND
                s.weight_sample_2 = s.weight_sample_3 AND
                s.status = 4 AND
                DATE(s.reception_timestamp) BETWEEN :start AND :end
            ORDER BY `s`.`delivery_date` DESC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function portioningInfo($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(t.started_at) AS 'Date',
                t.task_id AS 'Portioning task ID',
                TIME(t.started_at) AS 'Pull time',
                TIMEDIFF(t.finished_at,t.started_at) AS 'Total task time (hh:mm:ss)',
                CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2)) AS 'Total task time (decimal)',
                TIMEDIFF(ttp.finished_at,ttp.started_at) AS 'Actual portioning time (hh:mm:ss)',
                CAST(TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at)) / (60 * 60) AS DECIMAL(10, 2)) AS 'Actual portioning time (decimal)',
                SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) - TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at)) - TIME_TO_SEC(TIMEDIFF(task_verify_portioning.started_at,ttp.finished_at)) - TIME_TO_SEC(TIMEDIFF(task_verify_portioning.finished_at,task_verify_portioning.started_at))) AS 'Fluff time (hh:mm:ss)',
                CAST((TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) - TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at)) - TIME_TO_SEC(TIMEDIFF(task_verify_portioning.started_at,ttp.finished_at)) - TIME_TO_SEC(TIMEDIFF(task_verify_portioning.finished_at,task_verify_portioning.started_at))) / (60 * 60) AS DECIMAL(10, 2)) AS 'Fluff time (decimal)',
                IF(task_verify_portioning.started_at != '0000-00-00 00:00:00', TIMEDIFF(task_verify_portioning.started_at,ttp.finished_at), '-') AS 'QA wait time (hh:mm:ss)',
                IF(task_verify_portioning.started_at != '0000-00-00 00:00:00', CAST(TIME_TO_SEC(TIMEDIFF(task_verify_portioning.started_at,ttp.finished_at)) / (60 * 60) AS DECIMAL(10, 2)), '-') AS 'QA wait time (decimal)',
                IF(TIMEDIFF(task_verify_portioning.finished_at,task_verify_portioning.started_at) != '0000-00-00 00:00:00', TIMEDIFF(task_verify_portioning.finished_at,task_verify_portioning.started_at), '-') AS 'QA task time (hh:mm:ss)',
                IF(TIMEDIFF(task_verify_portioning.finished_at,task_verify_portioning.started_at) != '0000-00-00 00:00:00', CAST(TIME_TO_SEC(TIMEDIFF(task_verify_portioning.finished_at,task_verify_portioning.started_at)) / (60 * 60) AS DECIMAL(10, 2)), '-') AS 'QA task time (decimal)',
                u.user_email AS 'Portioner',
                u.internal_employee_id AS 'Employee ID',
                ttp.product_id AS 'Product ID',
                p.name AS 'Product name (fr)', 
                pl.l_name AS 'Product name (en)', 
                s.name AS 'Supplier',
                p.weight AS 'Weight (g)',
                CASE
                	WHEN p.charge_by_weight = 1 THEN 'Y'
                    ELSE 'N'
                END AS 'CBW',
                CASE
                	WHEN p.manipulation_needed = 3 THEN 'Y'
                    ELSE 'N'
                END AS 'DP',
                ttp.lot_id AS 'LOT ID',
                CASE
                    WHEN p.conservation_mode_in_warehouse = 0 THEN 'AMBIENT'
                    WHEN p.conservation_mode_in_warehouse = 1 THEN 'FRIDGE_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 2 THEN 'FRIDGE_15_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 3 THEN 'FREEZER'
                    WHEN p.conservation_mode_in_warehouse = 4 THEN 'OFFSITE_FRIDGE_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 5 THEN 'OFFSITE_FREEZER'
                    WHEN p.conservation_mode_in_warehouse = 6 THEN 'FRIDGE_8_DEGREES'
                    ELSE 'UNKNOWN - WEIRD'
                END AS 'Storage temperature at Lufa',
                psg.name AS 'Portioning category',
                psg.seconds_per_portion AS 'Default portioning speed',
                ttp.expected_portion_speed AS 'Expected efficiency',
                ttp.quantity_portioned AS 'Portions done',
                ROUND((TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)))/ttp.quantity_portioned,2)  AS 'Avg. time per portion (s) (including fluff)',
                ROUND((TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at)))/ttp.quantity_portioned,2)  AS 'Avg. time per portion (s) (without fluff)',
                SEC_TO_TIME(ttp.quantity_portioned * psg.seconds_per_portion) AS 'Default task time (hh:mm:ss)',
                CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) AS 'Default task time (decimal)',
                IF(CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) - CAST(TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at)) / (60 * 60) AS DECIMAL(10, 2)) > 0, SEC_TO_TIME((ttp.quantity_portioned * psg.seconds_per_portion) - TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at))), '-') AS 'Time saved (hh:mm:ss)',
                GREATEST(0, CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) - CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2))) AS 'Time saved (decimal)',
                15.25 AS 'Hourly rate',
                ROUND((15.25 * GREATEST(0, CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) - CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2))) * 0.5), 2) AS 'Task bonus at 50%',
                ROUND((15.25 * GREATEST(0, CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) - CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2))) * 0.5), 2) / CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2)) AS 'Hourly bonus @ 50%',
                IF(
                    ROUND((15.25 * GREATEST(0, CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) - CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2))) * 0.5), 2) / CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2)) > 3,
                    3 * CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2)),
                    ROUND((15.25 * GREATEST(0, CAST((ttp.quantity_portioned * psg.seconds_per_portion) / (60 * 60) AS DECIMAL(10, 2)) - CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2))) * 0.5), 2) / CAST(TIME_TO_SEC(TIMEDIFF(t.finished_at,t.started_at)) / (60 * 60) AS DECIMAL(10, 2)) * CAST(TIME_TO_SEC(TIMEDIFF(ttp.finished_at,ttp.started_at)) / (60 * 60) AS DECIMAL(10, 2))
                ) AS 'Total payout for Lufa',
                qa_user.user_email AS 'QA performed by',
                IF(task_type_quality_portioning.action_taken = 'Edited', 'Yes', 'No') AS 'Edits were made during the QA Task',
                IFNULL(task_type_quality_portioning.comment, '-') AS 'QA comment'
            FROM 
                `task_type_portioning` ttp
            INNER JOIN task t ON (t.task_id = ttp.task_id AND t.task_type_defaults_id = 8 AND t.status = 'Done')
            INNER JOIN products p ON (p.product_id = ttp.product_id)
            LEFT JOIN productsLang pl ON (pl.product_id = ttp.product_id AND pl.lang_id = 'en')
            LEFT JOIN suppliers s ON (p.supplier_id = s.supplier_id)
            LEFT JOIN portioning_speed_group psg ON (psg.portioning_speed_group_id = p.portioning_speed_group_id)
            LEFT JOIN task_type_quality_portioning ON (task_type_quality_portioning.task_id_to_qa = ttp.task_id AND action_taken IN ('Closed', 'Edited'))
            LEFT JOIN task task_verify_portioning ON (task_verify_portioning.task_id = task_type_quality_portioning.task_id AND task_verify_portioning.task_type_defaults_id = 13)
            INNER JOIN users u ON (t.assigned_to = u.user_id)
            LEFT JOIN users qa_user ON (qa_user.user_id = task_verify_portioning.assigned_to)
            WHERE 
                DATE(t.started_at) BETWEEN :start AND :end";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function numberOfBasketsShipped($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ o.delivery_date 'Date',
                sum(o.number_box_needed) 'Boxes'
            FROM orders o
            WHERE
                o.status = 4 AND 
                o.delivery_date BETWEEN :start AND :end
            GROUP BY 
                o.delivery_date";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function assetsScannedBetweenTwoDates($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ asset.srl_nbr,
                asset_activity.created,
                asset_location.name
            FROM 
                `asset_activity`
            INNER JOIN asset ON (asset.asset_id = asset_activity.asset_id)
            INNER JOIN asset_location ON (asset_location.location_id = asset_activity.location_id)
            WHERE asset_activity.`location_id` IN (1,8) AND DATE(asset_activity.`created`) BETWEEN :start AND :end';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function assetsTrackingReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ asset.srl_nbr AS 'Serial #',
                CASE
                    WHEN asset.active = 1 THEN 'Active'
                    WHEN asset.active = 2 THEN 'Broken'
                    WHEN asset.active = 3 THEN 'Bought by customer'
                    WHEN asset.active = 4 THEN 'Lost'
                    WHEN asset.active = 5 THEN 'Customer posession'
                    WHEN asset.active = 8 THEN 'On cart'
                    ELSE asset.active
                END AS 'Status',
                DATE_FORMAT(asset.updated, '%Y-%m-%d') AS 'Last activity',
                asset.last_client_id AS 'Customer ID',
                asset.last_order_id AS 'Order ID',
                IF(orders.type IS NULL, '-', IF(orders.type = 4, 'Yes', 'No')) AS 'Trial basket order?',
                IF(wws_hd.step_id IS NOT NULL, 'HD', (IF(wws_pup.step_id IS NOT NULL, 'PUP', '-'))) AS 'Delivery type',
                IFNULL(wws_hd.address, wws_pup.address) AS 'Delivery location',
                IF(orders.droppoint_id IN (SELECT droppoint_id FROM droppoints WHERE hd_product_id IS NOT NULL), REPLACE(UPPER(uhd.zip_code), ' ', ''), REPLACE(UPPER(d.zip_code), ' ', '')) AS 'Postal code',
                IF(wwr_hd.route_id IS NOT NULL, wwr_hd.route_id, (IF(wwr_pup.route_id IS NOT NULL, wwr_pup.route_id, '-'))) AS 'Delivery route',
                IF(driver_hd.user_id IS NOT NULL, CONCAT(driver_hd.first_name, ' ', driver_hd.last_name), (IF(driver_pup.user_id IS NOT NULL, CONCAT(driver_pup.first_name, ' ', driver_pup.last_name), '-'))) AS 'Delivery driver',
                IF(wwc_hd.name IS NOT NULL, wwc_hd.name, (IF(wwc_pup.name IS NOT NULL, wwc_pup.name, '-'))) AS 'Delivery contractor',
                last_order.last_order_date AS 'Customer s last order to date',
                asset.asset_id AS 'Asset ID',
                asset.created AS 'Asset created at'
                
            FROM 
                `asset` 
            
            LEFT JOIN orders ON (orders.order_id = asset.last_order_id)
            
            LEFT JOIN user_home_deliveries uhd ON (uhd.order_id = asset.last_order_id)
            LEFT JOIN droppoints d ON (orders.droppoint_id = d.droppoint_id)
            
            LEFT JOIN ww_orders wwo_hd ON (wwo_hd.order_number = orders.order_id)
            LEFT JOIN ww_steps wws_hd ON (wws_hd.order_id = wwo_hd.order_id AND wws_hd.type = 0)
            LEFT JOIN ww_routes wwr_hd ON (wwr_hd.route_id = wwo_hd.route_id)
            LEFT JOIN ww_vehicles wwv_hd ON (wwv_hd.vehicle_id = wwr_hd.vehicle_id)
            LEFT JOIN companies wwc_hd ON (wwc_hd.company_id = wwv_hd.company_id)
            LEFT JOIN users driver_hd ON (driver_hd.user_id = wws_hd.user_id)
            
            LEFT JOIN ww_orders wwo_pup ON (wwo_pup.drop_instance_id = orders.drop_instance_id AND orders.delivery_date = wwo_pup.delivery_date)
            LEFT JOIN ww_steps wws_pup ON (wws_pup.order_id = wwo_pup.order_id AND wws_pup.type = 0)
            LEFT JOIN ww_routes wwr_pup ON (wwr_pup.route_id = wwo_pup.route_id)
            LEFT JOIN ww_vehicles wwv_pup ON wwv_pup.vehicle_id = wwr_pup.vehicle_id
            LEFT JOIN companies wwc_pup ON (wwc_pup.company_id = wwv_pup.company_id)
            LEFT JOIN users driver_pup ON (driver_pup.user_id = wws_pup.user_id)
            
            LEFT JOIN (
                SELECT 
                    user_id, 
                    MAX(delivery_date) as last_order_date
                FROM 
                    orders
                WHERE 
                    status = 4
                GROUP BY 
                    user_id
            ) last_order ON (last_order.user_id = asset.last_client_id)
            
            WHERE 
                asset.fake_asset = 0 AND 
                DATE(asset.updated) BETWEEN :start AND :end";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function eufStrategy($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ YEAR(tmp.delivery_date) year,
                MONTH(tmp.delivery_date) month,
                SUM(tmp.hd_gen) 'hd orders gen.',
                SUM(tmp.pup_gen) 'pup orders gen.',
                SUM(tmp.hd_ship) 'hd orders ship.',
                SUM(tmp.pup_ship) 'pup orders ship.',
                ROUND(SUM(tmp.hd_rev)/SUM(tmp.hd_ship),2) 'hd abp',
                ROUND(SUM(tmp.pup_rev)/SUM(tmp.pup_ship),2) 'pup abp'
            FROM (
                SELECT
                    o.delivery_date,
                    SUM(if(d.hd_product_id IS NOT NULL, 1, 0)) hd_gen,
                    SUM(if(d.hd_product_id IS NULL, 1, 0)) pup_gen,
                    SUM(if(d.hd_product_id IS NOT NULL and o.status = 4, 1, 0)) hd_ship,
                    SUM(if(d.hd_product_id IS NULL and o.status = 4, 1, 0)) pup_ship,
                    SUM(if(d.hd_product_id IS NOT NULL, o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount, 0)) as hd_rev,
                    SUM(if(d.hd_product_id IS NULL, o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount, 0)) as pup_rev
                FROM 
                    orders o
                    LEFT JOIN	
                    droppoints d ON d.droppoint_id = o.droppoint_id
                WHERE 
                    o.delivery_date >= '2020-02-01'
                GROUP BY 
                    o.delivery_date
            ) tmp
            GROUP BY 
                YEAR(tmp.delivery_date), 
                MONTH(tmp.delivery_date)";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function subscriptionsPerWeekday($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ di.delivery_day 'day', 
                count(*) 'count'
            FROM subscriptions s
            INNER JOIN drop_instance di ON (di.drop_instance_id = s.drop_instance_id)
            WHERE 
                s.active = 1
            GROUP BY 
                di.delivery_day";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function eufQuery($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ SUBSTRING(UPPER(REPLACE(u.zip_code, ' ', '')), 1, 3) 'postal',
                actives.count 'actives',
                COUNT(*) 'generated',
                SUM(IF(o.status = 4, 1, 0)) 'shipped',
                SUM(IF(o.status = 4, 1, 0)) / count(*) 'take rate',
                SUM(IF(o.status = 4, o.total_order_amount - o.total_national_tax - o.total_provincial_tax + o.discount - o.delivery_service_amount, 0)) / sum(if(o.status = 4, 1, 0)) 'abp',
                SUM(IF(o.subscription_id = 0, 1, 0)) 'extra basket'
            FROM orders o
            INNER JOIN users u ON u.user_id = o.user_id
            INNER JOIN (
                SELECT
                    SUBSTRING(UPPER(REPLACE(u.zip_code, ' ', '')), 1, 3) 'postal',
                    SUM(IF(ssc.new_state IN (4,5,7,8,9,10,11), 1, 0)) 'count'
                from users u
                INNER JOIN subscriptions s ON s.user_id = u.user_id
                INNER JOIN (
                    SELECT
                        ssc.subscription_id, 
                        max(ssc.subscription_state_changes_id) 'change_id'
                    FROM subscription_state_changes ssc
                    WHERE
                        ssc.created_at < :start
                    GROUP BY
                        ssc.subscription_id
                ) last_change ON s.subscription_id = last_change.subscription_id
                INNER JOIN subscription_state_changes ssc ON ssc.subscription_state_changes_id = last_change.change_id
                GROUP BY 
                    SUBSTRING(UPPER(REPLACE(u.zip_code, ' ', '')), 1, 3)
            ) actives ON actives.postal = SUBSTRING(UPPER(REPLACE(u.zip_code, ' ', '')), 1, 3)
            WHERE
                o.delivery_date BETWEEN :start AND :end
            GROUP BY
                SUBSTRING(UPPER(REPLACE(u.zip_code, ' ', '')), 1, 3)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function numberOfInventoryChecks($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "select
                /*+ MAX_EXECUTION_TIME(900000) */ date(t.finished_at) 'date',
                u.user_email 'user',
                tti.lot_id 'lot',
                i.product_id 'product',
                case tti.reason_id
                when 1 then 'not found'
                when 2 then 'recount'
                when 3 then 'clean'
                when 4 then 'bad quality'
                when 5 then 'horrible quality'
                when 6 then 'expiration'
                when 7 then 'liquidation'
                when 8 then 'verify portion'
                when 9 then 'low quantity'
                else '?'
                end 'type',
                timediff(t.finished_at, t.started_at) 'time',
                pau.user_email 'last put away by',
                pa.created 'last put away at'
            from task_type_inventorycheck tti
            inner join task t
                on t.task_id = tti.task_id
            inner join users u
                on u.user_id = t.assigned_to
            inner join inventory i
                on i.lot_id = tti.lot_id
            inner join put_away_history pa
                on pa.lot_id = tti.lot_id
            inner join users pau
                on pa.user_id = pau.user_id
            where
                t.status = 'Done'
                and t.finished_at between :start and :end
                and pa.put_away_history_id = (select max(put_away_history_id) from put_away_history where lot_id = tti.lot_id and created < tti.created_date)
            order by
                t.finished_at, u.user_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function flawlessPerWeek($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ WEEK(delivery_date, 6) AS 'WEEK NUMBER',
                COUNT(tmp.order_id) AS 'NB ORDERS SHIPPED',
                SUM(IF(nb_items_shorted>0, 1, 0)) AS 'NB ORDERS WITH A SHORT ISSUE',
                ROUND(SUM(IF(nb_items_shorted>0, 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH A SHORT ISSUE',
                SUM(IF(nb_items_refunded>0, 1, 0)) AS 'NB ORDERS WITH AN ITEM ISSUE',
                ROUND(SUM(IF(nb_items_refunded>0, 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH AN ITEM ISSUE',
                SUM(IF(late_or_not = 'LATE', 1, 0)) AS 'NB ORDERS DELIVERED LATE',
                ROUND(SUM(IF(late_or_not = 'LATE', 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS DELIVERED LATE',
                SUM(IF(delivery_type = 'HD' AND late_or_not = 'LATE', 1, 0)) AS 'NB HD ORDERS DELIVERED LATE',
                SUM(IF(delivery_type = 'PUP' AND late_or_not = 'LATE', 1, 0)) AS 'NB PUP/STA ORDERS DELIVERED LATE',
                ROUND(AVG(minutes_late)) AS 'AVERAGE LATENESS IN MINUTES',
                SUM(IF(nb_items_refunded>0 AND late_or_not = 'LATE', 1, 0)) AS 'NB ORDERS WITH AN ITEM ISSUE AND LATENESS',
                ROUND(SUM(IF(nb_items_refunded>0 AND late_or_not = 'LATE', 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH AN ITEM ISSUE AND LATENESS',
                SUM(IF(nb_items_refunded>0 OR late_or_not = 'LATE', 1, 0)) AS 'NB ORDERS WITH AN ITEM ISSUE OR LATENESS',
                ROUND(SUM(IF(nb_items_refunded>0 OR late_or_not = 'LATE', 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH AN ITEM ISSUE OR LATENESS'
            FROM (
                SELECT 
                    o.delivery_date,
                    o.order_id, 
                    o.droppoint_id,
                    SUM(IF(od.refunded_reason = 31, 1, 0)) AS nb_items_shorted,
                    SUM(IF(od.refunded_qty>0, 1, 0)) AS nb_items_refunded,
                    COUNT(od.order_details_id) AS nb_items,
                    IF(wws_hd.step_id IS NOT NULL, 'HD', 'PUP') AS delivery_type,
                    IF(wws_hd.step_id IS NOT NULL, IF(wws_hd.completed_datetime>wws_hd.limit_datetime, 'LATE', 'OK'), IF(wws_pup.completed_datetime>wws_pup.limit_datetime, 'LATE', 'OK')) AS late_or_not,
                    IF(wws_hd.step_id IS NOT NULL, IF(wws_hd.completed_datetime>wws_hd.limit_datetime, TIMESTAMPDIFF(MINUTE,wws_hd.limit_datetime,wws_hd.completed_datetime), NULL), IF(wws_pup.completed_datetime>wws_pup.limit_datetime, TIMESTAMPDIFF(MINUTE,wws_pup.limit_datetime,wws_pup.completed_datetime), NULL)) AS minutes_late
                FROM 
                    orders o
                INNER JOIN order_details od ON (od.order_id = o.order_id)
                LEFT JOIN ww_orders wwo_hd ON (wwo_hd.order_number = o.order_id)
                LEFT JOIN ww_steps wws_hd ON (wws_hd.order_id = wwo_hd.order_id AND wws_hd.type = 0)
                LEFT JOIN ww_orders wwo_pup ON (wwo_pup.drop_instance_id = o.drop_instance_id AND o.delivery_date = wwo_pup.delivery_date)
                LEFT JOIN ww_steps wws_pup ON (wws_pup.order_id = wwo_pup.order_id AND wws_pup.type = 0)
            
                WHERE
                    o.delivery_date BETWEEN :start AND :end AND 
                    o.status = 4
                GROUP BY 
                    o.order_id
            ) tmp
            GROUP BY 
                WEEK(delivery_date, 6)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function flawlessPerDay($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ WEEK(delivery_date, 6) AS 'WEEK NUMBER',
                tmp.delivery_date AS 'DELIVERY DATE',
                COUNT(tmp.order_id) AS 'NB ORDERS SHIPPED',
                SUM(IF(nb_items_shorted>0, 1, 0)) AS 'NB ORDERS WITH A SHORT ISSUE',
                ROUND(SUM(IF(nb_items_shorted>0, 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH A SHORT ISSUE',
                SUM(IF(nb_items_refunded>0, 1, 0)) AS 'NB ORDERS WITH AN ITEM ISSUE',
                ROUND(SUM(IF(nb_items_refunded>0, 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH AN ITEM ISSUE',
                SUM(IF(late_or_not = 'LATE', 1, 0)) AS 'NB ORDERS DELIVERED LATE',
                ROUND(SUM(IF(late_or_not = 'LATE', 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS DELIVERED LATE',
                SUM(IF(delivery_type = 'HD' AND late_or_not = 'LATE', 1, 0)) AS 'NB HD ORDERS DELIVERED LATE',
                SUM(IF(delivery_type = 'PUP' AND late_or_not = 'LATE', 1, 0)) AS 'NB PUP/STA ORDERS DELIVERED LATE',
                ROUND(AVG(minutes_late)) AS 'AVERAGE LATENESS IN MINUTES',
                SUM(IF(nb_items_refunded>0 AND late_or_not = 'LATE', 1, 0)) AS 'NB ORDERS WITH AN ITEM ISSUE AND LATENESS',
                ROUND(SUM(IF(nb_items_refunded>0 AND late_or_not = 'LATE', 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH AN ITEM ISSUE AND LATENESS',
                SUM(IF(nb_items_refunded>0 OR late_or_not = 'LATE', 1, 0)) AS 'NB ORDERS WITH AN ITEM ISSUE OR LATENESS',
                ROUND(SUM(IF(nb_items_refunded>0 OR late_or_not = 'LATE', 1, 0))/COUNT(tmp.order_id)*100, 2) AS '% ORDERS WITH AN ITEM ISSUE OR LATENESS'
            FROM (
                SELECT 
                    o.delivery_date,
                    o.order_id, 
                    o.droppoint_id,
                    SUM(IF(od.refunded_reason = 31, 1, 0)) AS nb_items_shorted,
                    SUM(IF(od.refunded_qty>0, 1, 0)) AS nb_items_refunded,
                    COUNT(od.order_details_id) AS nb_items,
                    IF(wws_hd.step_id IS NOT NULL, 'HD', 'PUP') AS delivery_type,
                    IF(wws_hd.step_id IS NOT NULL, IF(wws_hd.completed_datetime>wws_hd.limit_datetime, 'LATE', 'OK'), IF(wws_pup.completed_datetime>wws_pup.limit_datetime, 'LATE', 'OK')) AS late_or_not,
                    IF(wws_hd.step_id IS NOT NULL, IF(wws_hd.completed_datetime>wws_hd.limit_datetime, TIMESTAMPDIFF(MINUTE,wws_hd.limit_datetime,wws_hd.completed_datetime), NULL), IF(wws_pup.completed_datetime>wws_pup.limit_datetime, TIMESTAMPDIFF(MINUTE,wws_pup.limit_datetime,wws_pup.completed_datetime), NULL)) AS minutes_late
                FROM 
                    orders o
                INNER JOIN order_details od ON (od.order_id = o.order_id)
                LEFT JOIN ww_orders wwo_hd ON (wwo_hd.order_number = o.order_id)
                LEFT JOIN ww_steps wws_hd ON (wws_hd.order_id = wwo_hd.order_id AND wws_hd.type = 0)
                LEFT JOIN ww_orders wwo_pup ON (wwo_pup.drop_instance_id = o.drop_instance_id AND o.delivery_date = wwo_pup.delivery_date)
                LEFT JOIN ww_steps wws_pup ON (wws_pup.order_id = wwo_pup.order_id AND wws_pup.type = 0)
            
                WHERE
                    o.delivery_date BETWEEN :start AND :end AND 
                    o.status = 4
                GROUP BY 
                    o.order_id
            ) tmp
            GROUP BY 
                tmp.delivery_date";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function salesFromDiscountGroupByCategory($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ tmp.w AS 'WEEK',
                pc.name AS 'CATEGORY NAME',
                SUM(tmp.qty_sold) AS 'QUANTITY SOLD',
                SUM(tmp.sales) AS 'REVENUE',
                SUM(tmp.cost) AS 'COST',
                ROUND(100-(SUM(tmp.cost)/SUM(tmp.sales)*100),2) AS 'MARGIN',
                SUM(tmp.revenue_deals) AS 'REVENUE DEALS',
                SUM(tmp.cost_deals) AS 'COST DEALS',
                ROUND(100-((SUM(tmp.cost_deals)/SUM(tmp.revenue_deals))*100),2) AS 'MARGIN DEALS',
                ROUND((SUM(tmp.revenue_deals)/SUM(tmp.sales))*100,2) AS '% REVENUE FROM DEALS'
            FROM (
                SELECT 
                    YEARWEEK(o.delivery_date, 6) AS w,
                    od.product_id,
                    COUNT(od.order_details_id) AS qty_sold,
                    SUM(od.defined_retail_price_per_unit_for_default_weight) AS sales,
                    SUM(od.purchase_price_per_unit) AS cost,
                    SUM(IF(od.defined_retail_price_per_unit_for_default_weight<od.default_retail_price_per_unit, od.defined_retail_price_per_unit_for_default_weight, 0)) AS revenue_deals,
                    SUM(IF(od.defined_retail_price_per_unit_for_default_weight<od.default_retail_price_per_unit, od.purchase_price_per_unit, 0)) AS cost_deals
                FROM 
                    order_details od
                INNER JOIN orders o ON (od.order_id = o.order_id AND o.delivery_date BETWEEN :start AND :end AND o.status = 4)
                GROUP BY 
                    YEARWEEK(o.delivery_date), od.product_id
            ) tmp 
            INNER JOIN products p ON (p.product_id = tmp.product_id)
            INNER JOIN productSubSubCategories pssc ON (pssc.sub_sub_id = p.sub_sub_id)
            INNER JOIN productSubCategories psc ON (pssc.subcategory_id = psc.subcategory_id)
            INNER JOIN product_categories pc ON (pc.category_id = psc.category_id)
            INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
            WHERE 
            p.supplier_id NOT IN (1,113,376,597,922,935,972)
            GROUP BY 
                tmp.w, 
                pc.name";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function freshnessPerDay($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $output = [];

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ sales.delivery_date AS 'Sold on',
                sales.picked_from_lot_id AS 'Lot ID',
                p.product_id AS 'Product ID',
                IF(p.flow_through = 1, 'Yes', 'No') AS 'Flow-through Product?',
                p.name AS 'Product Name (fr)',
                CONCAT(u.first_name, ' ', u.last_name) AS 'Purchaser',
                CONCAT(u1.first_name, ' ', u1.last_name) AS 'Category Manager',
                pc.name AS 'Category (fr)',
                psc.name AS 'Sub Category (fr)',
                pl.l_name AS 'Product Name (en)',
                pcl.l_name AS 'Category (en)',
                pscl.l_name AS 'Sub Category (en)',
                sales.qty_sold AS 'Quantity sold that day',
                CASE
                    WHEN p.conservation_mode_in_warehouse = 0 THEN 'AMBIENT'
                    WHEN p.conservation_mode_in_warehouse = 1 THEN 'FRIDGE_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 2 THEN 'FRIDGE_15_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 3 THEN 'FREEZER'
                    WHEN p.conservation_mode_in_warehouse = 4 THEN 'OFFSITE_FRIDGE_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 5 THEN 'OFFSITE_FREEZER'
                    WHEN p.conservation_mode_in_warehouse = 6 THEN 'FRIDGE_8_DEGREES'
                    ELSE 'UNKNOWN - WEIRD'
                END AS 'Storage Temperature at Lufa',
                IF(sfo.prereceived_at IS NULL, i.created, sfo.prereceived_at) AS 'Pre-received at',
                '' AS 'Last pick of day',
                0 AS 'Number of minutes in inventory',
                IF(sfo.reception_timestamp IS NULL, DATEDIFF(sales.delivery_date, i.created), DATEDIFF(sales.delivery_date, sfo.reception_timestamp)) AS 'Days in Inventory Before Sale',
                i.Expiry_date AS 'Expiry Date',
                IF(sfo.reception_timestamp IS NULL, DATE(i.created), DATE(sfo.reception_timestamp)) AS 'Reception Date',
                p.default_duration_days_minimum_expiration AS 'Default Duration Days (min expiration)',
                p.days_remove_before_expiry_date AS 'Days before the expiration of a product it should be removed from sale'
            FROM 
                (SELECT 
                    o.delivery_date,
                    od.picked_from_lot_id,
                    COUNT(od.order_details_id) AS qty_sold
                FROM 
                    `order_details` od
                INNER JOIN orders o ON (o.order_id = od.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start AND :end)
                WHERE 
                    od.picked_from_lot_id>0
                GROUP BY
                    o.delivery_date,
                    od.picked_from_lot_id) sales
            INNER JOIN inventory i ON (i.lot_id = sales.picked_from_lot_id)
            INNER JOIN products p ON (p.product_id = i.product_id)
            LEFT JOIN productsLang pl ON (pl.product_id = p.product_id AND pl.lang_id = 'en')
            LEFT JOIN supplier_forecast_orders sfo ON (i.supplier_forecast_orders_id = sfo.supplier_forecast_orders_id AND sfo.supplier_forecast_orders_id>0)
            INNER JOIN productSubSubCategories pssc ON (p.sub_sub_id = pssc.sub_sub_id)
            INNER JOIN productSubCategories psc ON (pssc.subcategory_id = psc.subcategory_id)
            LEFT JOIN productSubCategoriesLang pscl ON (psc.subcategory_id = pscl.subcategory_id AND pscl.lang_id = 'en')
            INNER JOIN product_categories pc ON (pc.category_id = psc.category_id)
            LEFT JOIN productCategoriesLang pcl ON (pcl.category_id = pc.category_id AND pcl.lang_id = 'en')
            LEFT JOIN users u ON (u.user_id = p.purchaser_id)
            LEFT JOIN users u1 ON (u1.user_id = p.category_manager_id)";

        $data = Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll(); 
        foreach($data as $lot) {

            $output_row['Sold on'] = $lot['Sold on']; 
            $output_row['Lot ID'] = $lot['Lot ID']; 
            $output_row['Flow-through Product?'] = $lot['Flow-through Product?'];
            $output_row['Product ID'] = $lot['Product ID']; 
            $output_row['Product Name (fr)'] = $lot['Product Name (fr)']; 
            $output_row['Category (fr)'] = $lot['Category (fr)']; 
            $output_row['Sub Category (fr)'] = $lot['Sub Category (fr)']; 
            $output_row['Product Name (en)'] = $lot['Product Name (en)']; 
            $output_row['Category (en)'] = $lot['Category (en)']; 
            $output_row['Sub Category (en)'] = $lot['Sub Category (en)']; 
            $output_row['Quantity sold that day'] = $lot['Quantity sold that day']; 
            $output_row['Storage Temperature at Lufa'] = $lot['Storage Temperature at Lufa']; 
            $output_row['Pre-received at'] = $lot['Pre-received at']; 
            $output_row['Last pick of day'] = $lot['Last pick of day']; 
            $output_row['Number of minutes in inventory'] = $lot['Number of minutes in inventory']; 
            $output_row['Days in Inventory Before Sale'] = $lot['Days in Inventory Before Sale']; 
            $output_row['Expiry Date'] = $lot['Expiry Date']; 
            $output_row['Reception Date'] = $lot['Reception Date']; 
            $output_row['Default Duration Days (min expiration)'] = $lot['Default Duration Days (min expiration)']; 
            $output_row['Days before the expiration of a product it should be removed from sale'] = $lot['Days before the expiration of a product it should be removed from sale']; 

            $last_pick = Yii::app()->rodb->createCommand("SELECT created_at as last_pick FROM pick_activities WHERE type = 0 AND lot_id = :lot_id AND created_at BETWEEN :start AND :end ORDER BY created_at DESC LIMIT 1")->bindValues([':lot_id' => $lot['Lot ID'], ':start' => $lot['Sold on'].' 00:00:00', ':end' => $lot['Sold on'].' 23:59:59'])->queryRow();
            if(!empty($last_pick)) {
                $to_time = strtotime($last_pick['last_pick']);
                $from_time = strtotime($lot['Pre-received at']);
                $output_row['Number of minutes in inventory'] = round(abs($to_time - $from_time) / 60);
                $output_row['Last pick of day'] = $last_pick['last_pick'];
            } else {
                $output_row['Last pick of day'] = '-';
                $output_row['Number of minutes in inventory'] = '-';
            }
            $output[] = $output_row;
        }

        return $output;
    }

    public static function greenhouseSalesStats($params) {
        if(is_array($params['iso_week_year'])) {
            $params['iso_week_year'] = $params['iso_week_year']['id'];
        }
        $iso_week_year_splitted = explode('-', $params['iso_week_year']);
        $iso_week = $iso_week_year_splitted[1];
        $iso_year = $iso_week_year_splitted[0];

        $sql = "SELECT MIN(Calendar_Date) start_date, MAX(Calendar_Date) end_date FROM Dim_Date WHERE Billing_Week = :week AND ISO_Standard_Year = :year";
        $data = Yii::app()->rodb->createCommand($sql)->bindValue(':week', $iso_week)->bindValue(':year', $iso_year)->queryRow(); 
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];

        $sql = "SELECT 
            sales.delivery_date AS 'Sold_on',
            sales.picked_from_lot_id AS 'Lot_ID',
            p.product_id,
            p.supplier_id,
            sales.qty_sold AS 'Quantity_sold_that_day',
            IF(sfo.prereceived_at IS NULL, i.created, sfo.prereceived_at) AS 'Pre-received_at',
            0 AS 'Number_of_minutes_in_inventory',
            IF(sfo.reception_timestamp IS NULL, DATEDIFF(sales.delivery_date, i.created), DATEDIFF(sales.delivery_date, sfo.reception_timestamp)) AS 'Days_in_Inventory_Before_Sale'
        FROM 
            (SELECT 
                o.delivery_date,
                od.picked_from_lot_id,
                COUNT(od.order_details_id) AS qty_sold
            FROM 
                `order_details` od
            INNER JOIN orders o ON (o.order_id = od.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start_date AND :end_date)
            WHERE  
                od.picked_from_lot_id>0
            GROUP BY
                o.delivery_date,
                od.picked_from_lot_id) sales
        INNER JOIN inventory i ON (i.lot_id = sales.picked_from_lot_id)
        INNER JOIN products p ON (p.product_id = i.product_id AND p.supplier_id IN (1,113,376,597,922,972) AND p.crop_variety_id NOT IN (SELECT crop_variety_id FROM gh_crop_variety WHERE name LIKE '%Misfit%'))
        LEFT JOIN supplier_forecast_orders sfo ON (i.supplier_forecast_orders_id = sfo.supplier_forecast_orders_id AND sfo.supplier_forecast_orders_id>0)";
        $data_freshness = Yii::app()->rodb->createCommand($sql)->bindValue(':start_date', $start_date)->bindValue(':end_date', $end_date)->queryAll(); 
        foreach($data_freshness as $idx => $lot) {
            $last_pick = Yii::app()->rodb->createCommand("SELECT created_at as last_pick FROM pick_activities WHERE type = 0 AND lot_id = :lot_id AND DATE(created_at) BETWEEN :start AND :end ORDER BY created_at DESC LIMIT 1")->bindValues([':lot_id' => $lot['Lot_ID'], ':start' => $lot['Sold_on'].' 00:00:00', ':end' => $lot['Sold_on'].' 23:59:59'])->queryRow();
            if(!empty($last_pick)) {
                $to_time = strtotime($last_pick['last_pick']);
                $from_time = strtotime($lot['Pre-received_at']);
                $data_freshness[$idx]['Number_of_minutes_in_inventory'] = round(abs($to_time - $from_time) / 60);
            } else {
                $data_freshness[$idx]['Number_of_minutes_in_inventory'] = '-';
            }
        }

        $avg_freshness = [];
        $worst_freshness = [];
        foreach($data_freshness as $idx => $lot) {
            if(isset($avg_freshness[$lot['supplier_id']])) {
                $avg_freshness[$lot['supplier_id']]['minutes'] += $lot['Number_of_minutes_in_inventory'];
                $avg_freshness[$lot['supplier_id']]['count'] += 1;
            } else {
                $avg_freshness[$lot['supplier_id']]['minutes'] = $lot['Number_of_minutes_in_inventory'];
                $avg_freshness[$lot['supplier_id']]['count'] = 1;
            }
            if(isset($worst_freshness[$lot['supplier_id']])) {
                if($worst_freshness[$lot['supplier_id']]['minutes'] < $lot['Number_of_minutes_in_inventory']) {
                    $worst_freshness[$lot['supplier_id']]['minutes'] = $lot['Number_of_minutes_in_inventory'];
                    $worst_freshness[$lot['supplier_id']]['count'] = 1;
                    if($lot['supplier_id'] == 597) {
                        print_r($lot);
                    }
                }
            } else {
                $worst_freshness[$lot['supplier_id']]['minutes'] = $lot['Number_of_minutes_in_inventory'];
                $worst_freshness[$lot['supplier_id']]['count'] = 1;
            }
        }

        $avg_longest_freshness = [];
        foreach($data_freshness as $idx => $lot) {
            if(isset($avg_longest_freshness[$lot['supplier_id']])) {
                if(isset($avg_longest_freshness[$lot['supplier_id']][$lot['Lot_ID']])) {
                    if($avg_longest_freshness[$lot['supplier_id']][$lot['Lot_ID']] < $lot['Number_of_minutes_in_inventory']) {
                        $avg_longest_freshness[$lot['supplier_id']][$lot['Lot_ID']] = $lot['Number_of_minutes_in_inventory'];
                    }
                } else {
                    $avg_longest_freshness[$lot['supplier_id']][$lot['Lot_ID']] = $lot['Number_of_minutes_in_inventory'];
                }
            } else {
                $avg_longest_freshness[$lot['supplier_id']][$lot['Lot_ID']] = $lot['Number_of_minutes_in_inventory'];
            }
        }

        // print_r($avg_longest_freshness);

        $sql = "SELECT 
            YEARWEEK(ps.date, 6) as 'Year-Week',
            s.name as 'Supplier',
            s.supplier_id,    
            AVG(ps.availability_percentage) as 'Average availability',
            SUM(IF(ps.availability_nb_times_was_available > 0, availability_nb_times_was_available, 0)) AS total_availability_nb_times_was_available,
            SUM(IF(ps.availability_nb_times_was_available > 0, availability_nb_checks, 0)) AS total_availability_nb_checks,
            0 as 'Freshness',
            SUM(ps.qty_sold) as 'Units sold',
            ROUND(SUM(ps.gross_revenue_after_weight_adjustment),2) as 'Revenue',
            rating.avg_rating as 'Rating (Weekly)',
            0 as 'Rating (avg last 4 weeks)',
            ROUND(refunds.refunded_shorts_qty,2) as '# refunds (shorts)',
            IFNULL(ROUND(refunds.refunded_shorts_amount,2),0) as '$ refunds (shorts)',
            ROUND(refunds.refunded_not_shorts_qty,2) as '# refunds (other than shorts)',
            IFNULL(ROUND(refunds.refunded_not_shorts_qty,2),0) as '$ refunds (other than shorts)'
        FROM 
            product_stats ps 
        LEFT JOIN suppliers s ON (s.supplier_id = ps.supplier_id)
        LEFT JOIN (
            SELECT 
                YEARWEEK(pur.updated_at, 6) as yw,
                p.supplier_id,
                AVG(pur.rating) as avg_rating
            FROM 
                product_user_ratings pur 
            LEFT JOIN products p ON (p.product_id = pur.product_id)
            WHERE 
                DATE(pur.updated_at) BETWEEN :start_date AND :end_date
            GROUP BY
                YEARWEEK(pur.updated_at, 6),
                p.supplier_id
        ) rating ON (rating.supplier_id = ps.supplier_id AND rating.yw = YEARWEEK(ps.date, 6))
        LEFT JOIN (
            SELECT 
                p.supplier_id, 
                YEARWEEK(o.delivery_date, 6) as yw,
                SUM(IF(od.refunded_reason = 31, refunded_qty, 0)) as refunded_shorts_qty,
                SUM(IF(od.refunded_reason = 31, GREATEST(od.paid_price_for_real_weight, od.paid_price_per_unit_for_default_weight) * od.refunded_qty, 0)) as refunded_shorts_amount,
                SUM(IF(od.refunded_reason != 31, refunded_qty, 0)) as refunded_not_shorts_qty,
                SUM(IF(od.refunded_reason != 31, GREATEST(od.paid_price_for_real_weight, od.paid_price_per_unit_for_default_weight) * od.refunded_qty, 0)) as refunded_not_shorts_,
                COUNT(od.order_details_id) as qty_sold_backup
            FROM 
                order_details od
            INNER JOIN orders o ON (o.order_id = od.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start_date AND :end_date)
            INNER JOIN products p ON (p.product_id = od.product_id AND p.supplier_id IN (1,113,376,597,922,972))
            GROUP BY 
                p.supplier_id, 
                YEARWEEK(o.delivery_date, 6)
        ) refunds ON (refunds.supplier_id = ps.supplier_id AND refunds.yw = YEARWEEK(ps.date, 6))
        WHERE 
            ps.supplier_id IN (1,113,376,597,922,972) AND 
            ps.date BETWEEN :start_date AND :end_date AND 
            (ps.qty_sold>0 OR ps.availability_percentage>0)
        GROUP BY 
            ps.supplier_id,
            YEARWEEK(ps.date, 6)";
        $data = Yii::app()->rodb->createCommand($sql)->bindValue(':start_date', $start_date)->bindValue(':end_date', $end_date)->queryAll(); 
        $output = [];
        foreach($data as $idx => $row) {
            $output_row['Year-Week'] = $row['Year-Week']; 
            $output_row['Supplier'] = $row['Supplier']; 
            $output_row['Average availability'] = round(($row['total_availability_nb_times_was_available'] / $row['total_availability_nb_checks'] * 100), 2);
            // $output_row['Freshness (avg in hours)'] = round(($avg_freshness[$row['supplier_id']]['minutes'] / $avg_freshness[$row['supplier_id']]['count'])/60,2);
            $output_row['Freshness (avg in hours)'] = round((array_sum($avg_longest_freshness[$row['supplier_id']]) / sizeof($avg_longest_freshness[$row['supplier_id']]))/60,2);
            $output_row['Worst Freshness (in hours)'] = round($worst_freshness[$row['supplier_id']]['minutes']/60,2);
            $output_row['Rating (Weekly)'] = $row['Rating (Weekly)']; 
            $sql = "SELECT 
                IFNULL(AVG(pur.rating), '-') as avg_rating
            FROM 
                product_user_ratings pur 
            INNER JOIN products p ON (p.product_id = pur.product_id AND p.supplier_id = :supplier_id)
            WHERE 
                DATE(pur.updated_at) BETWEEN SUBDATE(:end_date, INTERVAL 4 WEEK) AND :end_date ";
            $avg_rating = Yii::app()->rodb->createCommand($sql)->bindValue(':end_date', $end_date)->bindValue(':supplier_id', $row['supplier_id'])->queryScalar();
            $output_row['Rating (avg last 4 weeks)'] = $avg_rating;
            $output_row['Units sold'] = $row['Units sold']; 
            $output_row['Revenue'] = $row['Revenue']; 
            $output_row['$ refunds (total)'] = ($row['$ refunds (shorts)'] + $row['$ refunds (other than shorts)']); 
            $output_row['# refunds (shorts)'] = $row['# refunds (shorts)']; 
            $output_row['$ refunds (shorts)'] = $row['$ refunds (shorts)']; 
            $output_row['# refunds (other than shorts)'] = $row['# refunds (other than shorts)']; 
            $output_row['$ refunds (other than shorts)'] = $row['$ refunds (other than shorts)']; 
        
            $output[] = $output_row;
        }
        return $output;
    }

    public static function clientsWhoPurchasedARecipe($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ r.name AS 'RECIPE NAME',
                psc.name AS 'TYPE',
                o.order_id AS 'ORDER ID',
                o.user_id AS 'USER ID',
                u.first_name AS 'FIRST NAME',
                u.last_name AS 'LAST NAME',
                o.delivery_date AS 'DELIVERY DATE'
            FROM 
                orders o 
            INNER JOIN recipeOrders ro ON (ro.order_id = o.order_id)
            INNER JOIN `recipes` r ON (ro.recipe_id = r.recipe_id)
            INNER JOIN users u ON (u.user_id = o.user_id)
            LEFT JOIN productSubCategories psc ON (psc.subcategory_id = r.subcategory_id)
            WHERE 
                o.delivery_date BETWEEN :start AND :end AND 
                o.status = 4";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function recipesSold($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ tmp2.delivery_date AS 'Delivery Date',
            dd.ISO_Standard_Week AS 'Week',
            tmp2.recipe_id AS 'Meal Kit ID',
            recipesLang.l_name AS 'Meal Kit Name (EN)',
            recipes.name AS 'Mealk Kit name (FR)',
            recipes.portions AS '# portions',
            pscl.l_name AS 'Category (EN)',
            psc.name AS 'Category (FR)',
            COUNT(tmp2.recipe_id) AS '# recipes bought',
            tmp2.nb_suggested_items AS '# of suggested ingredients',
            AVG(tmp2.nb_suggested_bought) AS 'Avg. # suggested bought',
            tmp2.nb_common_items AS '# of common ingredients',
            AVG(tmp2.nb_common_bought) AS 'Average # common bought',
            SUM(tmp2.nb_units_sold) AS 'Total units sold',
            SUM(tmp2.paid_recipe_price) AS 'Total revenue',
            AVG(tmp2.paid_recipe_price) AS 'Avg. revenue/recipe'
        FROM (
            SELECT 
                tmp.delivery_date,
                tmp.recipe_id,
                tmp.order_id,
                rs.nb_suggested_items,
                SUM(IF(item_type = 'suggested_item', 1, 0)) AS nb_suggested_bought,
                rs.nb_common_items,
                SUM(IF(item_type = 'common_item', 1, 0)) AS nb_common_bought,
                COUNT(tmp.product_id) AS nb_units_sold,
                SUM(paid_price) AS paid_recipe_price
            FROM(
                SELECT 
                    o.delivery_date,
                    od.recipe_id,
                    od.order_id,
                    od.product_id,
                    IF(od.paid_price_for_real_weight > 0, od.paid_price_for_real_weight, od.paid_price_per_unit_for_default_weight) AS paid_price,
                    IF(ri.common = 0, 'suggested_item', 'common_item') AS item_type
                FROM 
                    order_details od
                INNER JOIN orders o ON (o.order_id = od.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start AND :end)
                INNER JOIN recipes r ON (r.recipe_id = od.recipe_id AND r.is_bundle = 0)
                LEFT JOIN recipeIngredients ri ON (ri.recipe_id = od.recipe_id AND ri.product_id = od.product_id)
                WHERE 
                    od.recipe_id IS NOT NULL
            ) tmp
            LEFT JOIN (
                SELECT 
                    recipe_id, 
                    SUM(IF(common = 0, 1, 0)) AS nb_suggested_items, 
                    SUM(IF(common = 1, 1, 0)) AS nb_common_items 
                FROM 
                    recipeIngredients 
                WHERE 
                    `option` = 0 
                GROUP BY recipe_id
            ) rs ON (rs.recipe_id = tmp.recipe_id)
            GROUP BY 
                tmp.order_id,
                tmp.recipe_id
        ) tmp2
        LEFT JOIN Dim_Date dd ON (dd.Calendar_Date = tmp2.delivery_date)
        INNER JOIN recipes ON (recipes.recipe_id = tmp2.recipe_id)
        LEFT JOIN recipesLang ON (recipesLang.recipe_id = tmp2.recipe_id AND recipesLang.lang_id = 'en')
        LEFT JOIN productSubCategories psc ON (psc.subcategory_id = recipes.subcategory_id)
        LEFT JOIN productSubCategoriesLang pscl ON (pscl.subcategory_id = recipes.subcategory_id AND pscl.lang_id = 'en')
        GROUP BY 
            tmp2.delivery_date,
            tmp2.recipe_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function allPostalCodes($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ SUM(
                    IF(t.percent_capacity >= 95, 1, 0)
                ) * 100 / COUNT(*) AS 'Full Postal'
            FROM
                (
                SELECT
                    IFNULL(
                        (
                            s.capacity / s.capacity_max * 100
                        ),
                        0
                    ) AS percent_capacity
                FROM
                    droppoints dp
                LEFT JOIN(
                    SELECT
                        COUNT(s.subscription_id) AS capacity,
                        di.drop_instance_id,
                        di.capacity AS capacity_max,
                        di.droppoint_id AS dpid
                    FROM
                        subscriptions s,
                        drop_instance di
                    WHERE
                        s.active = 1 AND di.drop_instance_id = s.drop_instance_id AND s.`type` = 0 AND di.active = 1
                    GROUP BY
                        di.drop_instance_id
                ) AS s
            ON
                s.dpid = dp.droppoint_id
            WHERE
                dp.published = 1
            ) t";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function salesReportForAGivenDate($params)
    {
        if (empty($params['end'])) {
            throw new Exception('Missing date params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ sales.delivery_date 'date',
                sales.product_id 'product id',
                SUM(sales.qty_sold) 'quantity sold',
                SUM(
                    sales.qty_sold * sales.sale_price
                ) 'revenue',
                SUM(
                    sales.qty_sold * sales.purchase_price
                ) 'cost',
                (
                    100 -(
                        ROUND(
                            sales.purchase_price / sales.sale_price,
                            2
                        ) * 100
                    )
                ) 'margin',
                IF(
                    sales_featured.qty_sold IS NULL,
                    0,
                    SUM(
                        sales_featured.qty_sold * sales_featured.sale_price
                    )
                ) 'revenue featured',
                IF(
                    sales_featured.qty_sold IS NULL,
                    0,
                    SUM(
                        sales_featured.qty_sold * sales_featured.purchase_price
                    )
                ) 'cost featured',
                IF(
                    sales_featured.qty_sold IS NULL,
                    0,
                    (
                        100 -(
                            ROUND(
                                sales_featured.purchase_price / sales_featured.sale_price,
                                2
                            ) * 100
                        )
                    )
                ) 'margin featured',
                IF(
                    sales_discounted.qty_sold IS NULL,
                    0,
                    SUM(
                        sales_discounted.qty_sold * sales_discounted.sale_price
                    )
                ) 'revenue deals',
                IF(
                    sales_discounted.qty_sold IS NULL,
                    0,
                    SUM(
                        sales_discounted.qty_sold * sales_discounted.purchase_price
                    )
                ) 'cost deals',
                IF(
                    sales_discounted.qty_sold IS NULL,
                    0,
                    (
                        100 -(
                            ROUND(
                                sales_discounted.purchase_price / sales_discounted.sale_price,
                                2
                            ) * 100
                        )
                    )
                ) 'margin deals',
                IF(
                    sales_built.qty_sold IS NULL,
                    0,
                    SUM(
                        sales_built.qty_sold * sales_built.sale_price
                    )
                ) 'revenue built',
                IF(
                    sales_built.qty_sold IS NULL,
                    0,
                    SUM(
                        sales_built.qty_sold * sales_built.purchase_price
                    )
                ) 'cost built',
                IF(
                    sales_built.qty_sold IS NULL,
                    0,
                    (
                        100 -(
                            ROUND(
                                sales_built.purchase_price / sales_built.sale_price,
                                2
                            ) * 100
                        )
                    )
                ) 'margin built'
            FROM
                (
                SELECT
                    o.delivery_date,
                    od.product_id,
                    od.purchase_price_per_unit purchase_price,
                    PI.current_price sale_price,
                    COUNT(od.order_details_id) qty_sold
                FROM
                    order_details od,
                    orders o,
                    products_inventory PI
                WHERE
                    o.order_id = od.order_id AND PI.product_id = od.product_id AND PI.inventory_date = o.delivery_date AND o.status = 4 AND o.delivery_date = :end
                GROUP BY
                    o.delivery_date,
                    od.product_id
            ) sales
            LEFT JOIN(
                SELECT o.delivery_date,
                    od.product_id,
                    od.purchase_price_per_unit purchase_price,
                    PI.current_price sale_price,
                    COUNT(od.order_details_id) qty_sold
                FROM
                    order_details od,
                    orders o,
                    products p,
                    products_inventory PI
                WHERE
                    o.order_id = od.order_id AND p.product_id = od.product_id AND PI.product_id = od.product_id AND PI.inventory_date = o.delivery_date AND o.status = 4 AND o.delivery_date = :end AND PI.is_featured = 1
                GROUP BY
                    o.delivery_date,
                    od.product_id
            ) sales_featured
            ON
                sales.delivery_date = sales_featured.delivery_date AND sales.product_id = sales_featured.product_id
            LEFT JOIN(
                SELECT o.delivery_date,
                    od.product_id,
                    od.purchase_price_per_unit purchase_price,
                    PI.current_price sale_price,
                    COUNT(od.order_details_id) qty_sold
                FROM
                    order_details od,
                    orders o,
                    products p,
                    products_inventory PI
                WHERE
                    o.order_id = od.order_id AND p.product_id = od.product_id AND PI.product_id = od.product_id AND PI.inventory_date = o.delivery_date AND o.status = 4 AND o.delivery_date = :end AND p.price > PI.current_price AND PI.default_price > PI.current_price
                GROUP BY
                    o.delivery_date,
                    od.product_id
            ) sales_discounted
            ON
                sales.delivery_date = sales_discounted.delivery_date AND sales.product_id = sales_discounted.product_id
            LEFT JOIN(
                SELECT o.delivery_date,
                    od.product_id,
                    od.purchase_price_per_unit purchase_price,
                    PI.current_price sale_price,
                    COUNT(od.order_details_id) qty_sold
                FROM
                    order_details od,
                    orders o,
                    basket_design_product_availability bdpa,
                    products_inventory PI
                WHERE
                    o.order_id = od.order_id AND PI.product_id = od.product_id AND PI.inventory_date = o.delivery_date AND od.product_id = bdpa.product_id AND o.basket_design_id = bdpa.basket_design_id AND o.status = 4 AND o.delivery_date = :end AND bdpa.default_quantity > 0
                GROUP BY
                    o.delivery_date,
                    od.product_id
            ) sales_built
            ON
                sales.delivery_date = sales_built.delivery_date AND sales.product_id = sales_built.product_id
            INNER JOIN products p ON
                sales.product_id = p.product_id
            WHERE
                p.supplier_id NOT IN(1, 113, 376, 597, 922, 972)
            GROUP BY
                sales.product_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':end', $params['end'])->queryAll();
    }

    public static function detailsForRefundsOfTypeQualityComplains($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ t.product_id,
                IF(t.type = 33, 'Packing', IF(t.type = 117, 'Portionning', IF(t.type = 118, 'Supplier', 'Cold Chain'))) as 'type',
                p.name AS 'product name',
                s.name  AS 'supplier name',
                COUNT(t.product_id) AS 'times refunded', 
                SUM(t.amount) AS amount_refunded,
            FROM
                transactions t
            LEFT JOIN 
                products p
                ON
                (t.product_id = p.product_id)
            LEFT JOIN
                suppliers s
                ON
                (p.supplier_id = s.supplier_id)
            WHERE
                DATE(t.created_time) BETWEEN :start AND :end
                AND t.type IN (33, 117, 118, 119)
                AND t.finalized = 1
                AND t.child_transaction_id <= 0
            GROUP BY 
                t.product_id, t.type";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function detailsForRefundsOfTypeQualityComplains33($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ t.product_id,
                p.name AS 'product name',
                s.name  AS 'supplier name',
                COUNT(t.product_id) AS 'times refunded', 
                SUM(t.amount) AS amount_refunded
            FROM
                transactions t
            LEFT JOIN 
                products p
                ON
                (t.product_id = p.product_id)
            LEFT JOIN
                suppliers s
                ON
                (p.supplier_id = s.supplier_id)
            WHERE
                DATE(t.created_time) BETWEEN :start AND :end
                AND t.type = 33
                AND t.finalized = 1
                AND t.child_transaction_id <= 0
            GROUP BY 
                t.product_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function shorts($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ CONCAT(u.first_name, ' ', u.last_name) AS 'Purchaser',
                CONCAT(u1.first_name, ' ', u1.last_name) AS 'Category Manager',
                p.product_id 'Product ID',
                p.name 'Product Name',
                s.name 'Supplier Name',
                CASE
                    WHEN p.conservation_mode_in_warehouse = 0 THEN 'WAREHOUSE_CONSERVATION_AMBIENT'
                    WHEN p.conservation_mode_in_warehouse = 1 THEN 'WAREHOUSE_CONSERVATION_REFRIGERATED_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 2 THEN 'WAREHOUSE_CONSERVATION_REFRIGERATED_15_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 3 THEN 'WAREHOUSE_CONSERVATION_FROZEN'
                    WHEN p.conservation_mode_in_warehouse = 4 THEN 'WAREHOUSE_CONSERVATION_OFFSITE_REFRIGERATED_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 5 THEN 'WAREHOUSE_CONSERVATION_OFFSITE_FROZEN'
                    WHEN p.conservation_mode_in_warehouse = 6 THEN 'WAREHOUSE_CONSERVATION_REFRIGERATED_8_DEGREES'
                    ELSE 'UNKNOWN - WEIRD'
                END AS 'Storage Temp',
                if(p.flow_through = 1, 'Y', 'N') 'Flow through (Y/N)',
                sum(ttp.quantity) 'Quantity Sold',
                sum(ttp.quantity - ttp.quantity_put) 'Quantity Short',
                pi.current_price 'Product Price',
                (sum(ttp.quantity - ttp.quantity_put) * pi.current_price) 'Value of Shorts',
                ifnull(recv.quantity, 0) 'Quantity received the same day',
                o.delivery_date 'Date'
            from task_type_prepbasket ttp
            inner join orders o
                on o.order_id = ttp.order_id
            inner join products p
                on p.product_id = ttp.product_id
            inner join suppliers s
                on s.supplier_id = p.supplier_id
            left join users u
                on u.user_id = p.purchaser_id
            left join users u1
                on u1.user_id = p.category_manager_id
            inner join products_inventory pi
                on p.product_id = pi.product_id AND o.delivery_date = pi.inventory_date
            left join
                (
                select
                    delivery_date,
                    product_id,
                    sum(quantity_accepted) quantity
                from supplier_forecast_orders
                where
                    delivery_date between :start and :end
                    and status = 4
                group by
                    delivery_date, product_id
                ) recv
                on p.product_id = recv.product_id
                and o.delivery_date = recv.delivery_date
            where
                o.delivery_date between :start and :end and DATE(ttp.updated_at) between :start and :end
            group by
                o.delivery_date, ttp.product_id
            having
                `Quantity Short` > 0
            order by
                o.delivery_date, s.supplier_id, ttp.product_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function deliveriesWithLatenessRawData($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ c.name AS 'COMPANY',
            wwv.external_id AS 'CAR NAME',
            wws.order AS 'STOP NUMBER',
            CONCAT(driver.last_name, ' ', driver.first_name) AS 'DRIVER NAME',
            wwo.delivery_date AS 'DELIVERY DATE',
            IF(wwo.order_number>0, 
            'HD', 
            CASE
                WHEN di.type=1 THEN 'PUP'
                WHEN di.type=2 THEN 'PICKUP'
                WHEN di.type=3 THEN 'EMBASSY'
                WHEN di.type IS NULL THEN 'BREAK'
            END
            ) AS 'DELIVERY TYPE',
            
            IF(uhd.leave_basket IS NULL, '-', IF(uhd.leave_basket = 0, 'NOT ALLOWED TO LEAVE AT DOOR', 'LEAVE AT DOOR')) AS 'ALLOWED TO LEAVE AT DOOR ?',
            CASE
            WHEN wws.status=1 THEN 'DONE'
            WHEN wws.status=2 THEN 'CANCELED'
            WHEN wws.status=0 THEN 'INCOMPLETED'
            END AS 'DELIVERY STATUS',
            IF(wwo.order_number>0, CONCAT(uhd.address, ' ', uhd.zip_code, ' ', uhd.city_name), CONCAT(d.name, ' - ', d.address, ' ', d.zip_code, ' ', d.city_name)) AS 'DELIVERY ADDRESS',
            IF(uhd.zip_code IS NULL, d.zip_code, uhd.zip_code) 'POSTAL CODE',
            pickup.completed_datetime 'PICKUP CONFIRMED AT',
            lbri.departure_time AS 'DEPARTURE TIME',
            TIME(t.finished_at) AS 'CARTS FINISHED AT',
            TIME(end_of_loading.loading_time) AS 'LOADING FINISHED AT',
            TIMEDIFF(TIME(end_of_loading.loading_time),lbri.departure_time) AS 'DEPARTURE LATENESS',
            IF(wwo.order_number>0, 1, details_pups.nb_orders) AS '# CLIENTS',
            ROUND(IF(wwo.order_number>0, o.total_order_amount, details_pups.amount_of_goods),2) AS 'AMOUNT OF GOODS DELIVERED',
            IF(wwo.order_number>0, o.number_box_needed, details_pups.boxes) AS 'NUMBER OF BOXES TO SHIP',
            routing_jobs.service_time AS 'SERVICE TIME',
            (wws.distance_from_previous/1000) AS 'DISTANCE FROM LAST STOP (KM)',
            wws.scheduled_datetime AS 'SCHEDULED DELIVERY TIME',
            wws.limit_datetime AS 'TIME LIMIT FOR DELIVERY',
            wws.completed_datetime AS 'REAL DELIVERY TIME',
            TIMEDIFF(wws.limit_datetime,wws.completed_datetime) AS 'DELIVERY LATENESS',
            CASE
                WHEN wws.completed_datetime > wws.limit_datetime AND (wwo.order_number > 0) THEN 'YES'
                WHEN wws.completed_datetime > wws.limit_datetime AND (wwo.order_number IS NULL AND di.type IS NOT NULL) THEN 'YES'
                ELSE 'NO'
            END AS 'LATE',
            IF(TIME(wws.completed_datetime) < hd_di.business_opening_time, 'Yes', 'No') as 'Early HD',
            IF(wwo.order_number>0, hd_di.business_opening_time, null) as 'Start Of Time Window (HD)'
        FROM 
            ww_steps wws
        INNER JOIN ww_orders wwo ON (wwo.order_id = wws.order_id AND wwo.delivery_date BETWEEN :start AND :end) 
        INNER JOIN ww_routes wwr ON (wwr.route_id = wwo.route_id)
        INNER JOIN users driver ON (wws.user_id = driver.user_id)
        LEFT JOIN companies c ON (driver.company_id = c.company_id)
        LEFT JOIN user_home_deliveries uhd ON (uhd.order_id = wwo.order_number)
        LEFT JOIN drop_instance di ON (di.drop_instance_id = wwo.drop_instance_id)
        LEFT JOIN droppoints d ON (d.droppoint_id = di.droppoint_id)
        LEFT JOIN ww_vehicles wwv ON wwv.vehicle_id = wwr.vehicle_id
        LEFT JOIN load_basket_route_info lbri ON lbri.ww_route_id = wwr.route_id
        LEFT JOIN task_type_loadbasket ttl ON lbri.route_info_id = ttl.lbri_id
        LEFT JOIN task_parent_child ppc ON (ppc.child_id = ttl.task_id)
        LEFT JOIN task t ON (t.task_id = ppc.parent_id)
        LEFT JOIN orders o ON (o.order_id = wwo.order_number AND o.delivery_date BETWEEN :start AND :end)
        LEFT JOIN drop_instance hd_di ON (o.drop_instance_id = hd_di.drop_instance_id)
        LEFT JOIN (
            SELECT 
            delivery_date,
            COUNT(order_id) AS nb_orders,
            SUM(total_order_amount) AS amount_of_goods,
            SUM(number_box_needed) AS boxes,
            drop_instance_id
            FROM 
            orders 
            WHERE 
            status = 4 AND 
            delivery_date BETWEEN :start AND :end
            GROUP BY 
            delivery_date,
            drop_instance_id
        ) details_pups ON details_pups.drop_instance_id = wwo.drop_instance_id AND details_pups.delivery_date = wwo.delivery_date
        LEFT JOIN (
            SELECT 
                ttlb.lbri_id, 
                MAX(t.finished_at) AS loading_time
            FROM 
                `task_type_loadbasket` ttlb
            INNER JOIN 
                task t ON (t.task_id = ttlb.task_id) 
            WHERE 
                ttlb.lbri_id IN (SELECT route_info_id FROM load_basket_route_info WHERE date BETWEEN :start AND :end)
            GROUP BY 
                ttlb.lbri_id
        ) end_of_loading ON (end_of_loading.lbri_id = lbri.route_info_id)
        LEFT JOIN 
            routing_jobs ON (routing_jobs.route_id = wwo.route_id AND (routing_jobs.order_id = wwo.order_number OR routing_jobs.drop_instance_id = wwo.drop_instance_id))
        LEFT JOIN
            ww_steps pickup ON pickup.order_id = wws.order_id AND pickup.type = 1 
        WHERE
            wws.type IN (0,2) 
        GROUP BY 
            wws.step_id
        ORDER BY 
            wwv.external_id, 
            wwo.delivery_date,
            wws.order ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function deliveriesWithLatenessAggregatedData($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ COMPANY,
                `CAR NAME`,
                `DRIVER NAME`,
                `DELIVERY DATE`,
                `DEPARTURE TIME`,
                `CARTS FINISHED AT`,
                `LOADING FINISHED AT`,
                `DEPARTURE LATENESS`,
                SUM(IF(LATE = 'YES', 1, 0)) AS 'NUMBER OF STOPS LATE',
                SUM(IF(type != 2, 1, 0)) AS 'NUMBER OF STOPS',
                SUM(IF(LATE = 'YES', `AMOUNT OF GOODS DELIVERED`, 0)) AS 'AMOUNT OF GOODS DELIVERED LATE',
                SUM(IF(LATE = 'YES', `# CLIENTS`, 0)) AS '# CLIENTS DELIVERED LATE',
                ROUND(((SUM(IF(LATE = 'YES', 1, 0))/SUM(IF(type != 2, 1, 0)))*100),2) AS 'PERCENTAGE OF LATE STOPS',
                ROUND(SUM(distance_traveled)/1000,2) AS 'TRAVEL DISTANCE(KM)',
                SUM(boxes_needed) AS 'BASKETS IN TRUCK'
            FROM (
                SELECT 
                tmp2.*, 
                SUM(distance_to_next) as distance_traveled, 
                SUM(IF(type = 0, amt_goods_delivered, 0)) as 'AMOUNT OF GOODS DELIVERED', 
                IF(SUM(late_bool) > 0, 'YES', 'NO') as 'LATE'  
                FROM (
                SELECT 
                    c.name AS 'COMPANY',
                    wwv.external_id AS 'CAR NAME',
                    wwo.order_id,
                    wws.type,
                    wws.order AS 'STOP NUMBER',
                    CONCAT(driver.last_name, ' ', driver.first_name) AS 'DRIVER NAME',
                    wwo.delivery_date AS 'DELIVERY DATE',
                    IF(wwo.order_number>0, 
                    'HD', 
                    CASE
                        WHEN di.type=1 THEN 'PUP'
                        WHEN di.type=2 THEN 'PICKUP'
                        WHEN di.type=3 THEN 'EMBASSY'
                        WHEN di.type IS NULL THEN 'BREAK'
                    END
                    ) AS 'DELIVERY TYPE',
                    IF(wwo.order_number>0, CONCAT(uhd.address, ' ', uhd.zip_code, ' ', uhd.city_name), CONCAT(d.name, ' - ', d.address, ' ', d.zip_code, ' ', d.city_name)) AS 'DELIVERY ADDRESS',
                    lbri.departure_time AS 'DEPARTURE TIME',
                    TIME(t.finished_at) AS 'CARTS FINISHED AT',
                    TIME(end_of_loading.loading_time) AS 'LOADING FINISHED AT',
                    TIMEDIFF(TIME(end_of_loading.loading_time),lbri.departure_time) AS 'DEPARTURE LATENESS',
                    IF(wwo.order_number>0, 1, details_pups.nb_orders) AS '# CLIENTS',
                    ROUND(IF(wwo.order_number>0, o.total_order_amount, details_pups.amount_of_goods),2) AS amt_goods_delivered,
                    IF(wwo.order_number>0, o.number_box_needed, details_pups.boxes) AS 'NUMBER OF BOXES TO SHIP',
                    wws.limit_datetime AS 'TIME LIMIT FOR DELIVERY',
                    wws.completed_datetime AS 'REAL DELIVERY TIME',
                    TIMEDIFF(wws.limit_datetime,wws.completed_datetime) AS 'DELIVERY LATENESS',
                    IF(wws.completed_datetime > wws.limit_datetime, 1, 0) AS late_bool,
                    wws.distance_to_next,
                    IFNULL(o.number_box_needed, 0) + IFNULL(details_pups.boxes,0) as boxes_needed
                FROM 
                    ww_steps wws
                INNER JOIN ww_orders wwo ON (wwo.order_id = wws.order_id AND wwo.delivery_date BETWEEN :start AND :end) 
                INNER JOIN ww_routes wwr ON (wwr.route_id = wwo.route_id)
                INNER JOIN users driver ON (wws.user_id = driver.user_id)
                LEFT JOIN companies c ON (driver.company_id = c.company_id)
                LEFT JOIN user_home_deliveries uhd ON (uhd.order_id = wwo.order_number)
                LEFT JOIN drop_instance di ON (di.drop_instance_id = wwo.drop_instance_id)
                LEFT JOIN droppoints d ON (d.droppoint_id = di.droppoint_id)
                LEFT JOIN ww_vehicles wwv ON wwv.vehicle_id = wwr.vehicle_id
                LEFT JOIN load_basket_route_info lbri ON lbri.ww_route_id = wwr.route_id
                LEFT JOIN task_type_loadbasket ttl ON lbri.route_info_id = ttl.lbri_id
                LEFT JOIN task_parent_child ppc ON (ppc.child_id = ttl.task_id)
                LEFT JOIN task t ON (t.task_id = ppc.parent_id)
                LEFT JOIN orders o ON (o.order_id = wwo.order_number AND o.delivery_date BETWEEN :start AND :end)
                LEFT JOIN (
                    SELECT 
                    delivery_date,
                    COUNT(order_id) AS nb_orders,
                    SUM(total_order_amount) AS amount_of_goods,
                    SUM(number_box_needed) AS boxes,
                    drop_instance_id
                    FROM 
                    orders 
                    WHERE 
                    status = 4 AND 
                    delivery_date BETWEEN :start AND :end
                    GROUP BY 
                    delivery_date,
                    drop_instance_id
                ) details_pups ON details_pups.drop_instance_id = wwo.drop_instance_id AND details_pups.delivery_date = wwo.delivery_date
                LEFT JOIN (
                SELECT 
                    lbri_id, 
                    MAX(t.finished_at) AS loading_time
                    FROM 
                    `task_type_loadbasket` 
                    INNER JOIN task t ON (t.task_id = task_type_loadbasket.task_id) 
                    GROUP BY 
                    lbri_id
                ) end_of_loading ON (end_of_loading.lbri_id = lbri.route_info_id)
                
                GROUP BY 
                    wws.step_id
                ORDER BY 
                    wwv.external_id, 
                    wwo.delivery_date,
                    wws.order ASC
                ) tmp2
            GROUP BY 
                tmp2.order_id
            ) tmp
            GROUP BY 
                `CAR NAME`,
                `DELIVERY DATE`";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function deliveriesWithLatenessDataByRoute($params){
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }
        $sql = 
            "SELECT
            lbri.date AS 'Date',
            lbri.ww_route_id AS 'Route ID from TMS',
            boxes_delivered_per_routes.number_box_needed AS '# of Baskets',
            lbri.departure_time AS 'Scheduled Departure',
            lbri.ready_to_load AS 'Ready to Load time', 
            CASE
                WHEN lbri.ready_to_load IS NOT NULL AND lbri.ready_to_load > lbri.departure_time  THEN '#LATE#'
                WHEN lbri.ready_to_load IS NOT NULL AND lbri.ready_to_load < lbri.departure_time  THEN '#EARLY#'
                ELSE NULL
            END AS 'Ready to Load Status',
            cart_info.first_cart_loaded_at AS 'Start Time (First cart loaded at)',
            cart_info.last_cart_loaded_at AS 'Finish Time (Last cart loaded at)',
            IF(cart_info.last_cart_loaded_at IS NOT NULL AND ( cart_info.last_cart_loaded_at <= lbri.departure_time ), '#EARLY#', '#LATE#') AS 'Route Departure Status',
            IF(
                cart_info.first_cart_loaded_at IS NOT NULL AND lbri.ready_to_load IS NOT NULL,
                TIME_FORMAT(TIMEDIFF(TIME(cart_info.first_cart_loaded_at), TIME(lbri.ready_to_load)), '%Hh %im %ss'), NULL
            ) AS 'Duration Between Ready and Start',
            IF(
                cart_info.last_cart_loaded_at IS NOT NULL AND lbri.ready_to_load IS NOT NULL,
                TIME_FORMAT(TIMEDIFF(TIME(cart_info.last_cart_loaded_at), TIME(lbri.ready_to_load)), '%Hh %im %ss'), NULL
            ) AS 'Duration Between Ready and Finish'
        FROM
            load_basket_route_info lbri
        LEFT JOIN (
            SELECT
                ttlb.lbri_id,
                DATE_FORMAT(MIN(t.finished_at), '%H:%i:%s') AS first_cart_loaded_at,
                DATE_FORMAT(MAX(t.finished_at), '%H:%i:%s') AS last_cart_loaded_at
        FROM
            task_type_loadbasket ttlb
        INNER JOIN task t ON (t.task_id = ttlb.task_id)
        GROUP BY
            ttlb.lbri_id
        ) cart_info ON lbri.route_info_id = cart_info.lbri_id
        LEFT JOIN (
            SELECT 
                wwr.route_id,
                SUM(IFNULL(boxes_delivered_to_hd.number_box_needed, 0) + IFNULL(boxes_delivered_to_pups.number_box_needed, 0)) AS number_box_needed
            FROM 
                ww_routes wwr
            INNER JOIN ww_orders wwo ON (wwo.route_id = wwr.route_id)
            LEFT JOIN orders boxes_delivered_to_hd ON (boxes_delivered_to_hd.order_id = wwo.order_number)
            LEFT JOIN (
                SELECT 
                    o.delivery_date,
                    o.drop_instance_id, 
                    SUM(o.number_box_needed) AS number_box_needed
                FROM 
                    orders o 
                WHERE 
                    o.status = 4 AND o.delivery_date BETWEEN :start AND :end AND o.droppoint_id NOT IN (SELECT droppoint_id FROM droppoints WHERE hd_product_id IS NOT NULL)
                GROUP BY 
                    o.delivery_date,
                    o.drop_instance_id
            ) boxes_delivered_to_pups ON (boxes_delivered_to_pups.drop_instance_id = wwo.drop_instance_id AND boxes_delivered_to_pups.delivery_date = wwr.date)
            WHERE 
                wwr.date BETWEEN :start AND :end
            GROUP BY
                wwr.route_id
        ) boxes_delivered_per_routes ON (boxes_delivered_per_routes.route_id = lbri.ww_route_id)
        WHERE
            lbri.date BETWEEN :start AND :end
        ";
        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
      
    }

    public static function assetsScannedPerWeek($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "select /*+ MAX_EXECUTION_TIME(900000) */ year(created) 'year', week(created) 'week', count(*) from asset_activity where location_id = 3 and created between :start and :end group by year(created), week(created)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function icePackUsage($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "select
                /*+ MAX_EXECUTION_TIME(900000) */ yearweek(t.due_date, 6) 'week',
                p.product_id 'id',
                p.name 'product',
                sum(ttp.quantity_put) 'total used'
            from task_type_prepbasket ttp
            inner join products p
                on p.product_id = ttp.product_id
            inner join task t
                on t.task_id = ttp.task_id
            where
                ttp.status = 1
                and t.due_date between :start and :end
                and ttp.product_id in (4291,9756,3637,4292)
            group by 
                yearweek(t.due_date, 6), ttp.product_id
            order by
                yearweek(t.due_date, 6), ttp.product_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function incompletedSubcriptionsThatGotCompleted($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT CASE
            /*+ MAX_EXECUTION_TIME(900000) */ WHEN nb_orders = 0 THEN 'ACTIVATIONS'
            ELSE 'REACTIVATION'
            END AS label,
            COUNT(*) as 'TOTAL'
            FROM (
            select al.`user_id`, al.date, (SELECT COUNT(*) FROM securelufacom.orders WHERE orders.user_id = al.user_id AND orders.delivery_date < al.date) AS nb_orders
            from securelufacom.`action_log` al
            WHERE al.`date` BETWEEN :start AND :end AND al.`action_type` = 31
            ) t GROUP BY label";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function productsRatingsOverTime($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ tmp.d AS 'date',
                IF(tmp.product_id IS NOT NULL, 'product', 'recipe') AS 'type',
                IF(tmp.product_id IS NOT NULL, tmp.product_id, tmp.recipe_id) AS 'product id/recipe id',
                IF(tmp.product_id IS NOT NULL, p.name, r.name) AS 'product/recipe',
                IF(tmp.product_id IS NOT NULL, s.name, '-') AS 'supplier',
                pc.name 'category',
                psc.name 'subcategory',
                concat(purch.first_name, ' ', purch.last_name) 'purchaser',
                concat(subcategorymanager.first_name, ' ', subcategorymanager.last_name) 'Category Manager',
                tmp.rating 'rating',
                tmp.comment AS comment,
                concat(u.first_name, ' ', u.last_name) 'client',
                if(uc.phone_verified=1, uc.phone, '-') 'client phone'
            FROM (
                SELECT 
                    date(comments.created_at) AS d,
                    p.product_id,
                    r.recipe_id,
                    u.user_id,
                    IF(p.product_id IS NOT NULL, IFNULL(pur.rating, ''), IFNULL(rur.rating, '')) AS rating,
                    comments.comment_text AS comment
                FROM 
                    user_comments comments
                LEFT JOIN products p ON (p.product_id = comments.owner_id AND comments.owner_name = 'Product')
                LEFT JOIN recipes r ON (r.recipe_id = comments.owner_id AND comments.owner_name = 'Recipe')
                INNER JOIN users u ON (u.user_id = comments.user_id)
                LEFT JOIN product_user_ratings pur ON (pur.user_id = comments.user_id AND pur.product_id = comments.owner_id AND comments.owner_name = 'Product')
                LEFT JOIN recipe_user_ratings rur ON (rur.user_id = comments.user_id AND rur.recipe_id = comments.owner_id AND comments.owner_name = 'Recipe')
                WHERE 
                    comments.created_at BETWEEN :start AND :end
            
                UNION 
            
                SELECT 
                    date(pur.created_at) AS d,
                    p.product_id,
                    NULL AS recipe_id,
                    u.user_id,
                    IFNULL(pur.rating, '') AS rating,
                    '' AS comment
                FROM 
                    product_user_ratings pur
                INNER JOIN products p ON (p.product_id = pur.product_id)
                INNER JOIN users u ON (u.user_id = pur.user_id)
                LEFT JOIN user_comments ON (user_comments.owner_id = p.product_id AND user_comments.user_id = u.user_id AND user_comments.created_at BETWEEN :start AND :end)
                WHERE 
                    pur.created_at BETWEEN :start AND :end AND 
                    user_comments.comment_id IS NULL
            
                UNION 
            
                SELECT 
                    date(rur.created_at) AS d,
                    NULL AS product_id,
                    r.recipe_id,
                    u.user_id,
                    IFNULL(rur.rating, '') AS rating,
                    '' AS comment
                FROM 
                    recipe_user_ratings rur
                LEFT JOIN recipes r ON (r.recipe_id = rur.recipe_id)
                INNER JOIN users u ON (u.user_id = rur.user_id)
                LEFT JOIN user_comments ON (user_comments.owner_id = r.recipe_id AND user_comments.user_id = u.user_id AND user_comments.created_at BETWEEN :start AND :end)
                WHERE 
                    rur.created_at BETWEEN :start AND :end AND 
                    user_comments.comment_id IS NULL
            
            ) tmp
            LEFT JOIN products p on p.product_id = tmp.product_id
            LEFT JOIN recipes r on r.recipe_id = tmp.recipe_id
            LEFT JOIN suppliers s on s.supplier_id = p.supplier_id
            LEFT JOIN productSubSubCategories pssc on p.sub_sub_id = pssc.sub_sub_id
            LEFT JOIN productSubCategories psc on pssc.subcategory_id = psc.subcategory_id
            LEFT JOIN product_categories pc on psc.category_id = pc.category_id
            LEFT JOIN users purch on p.purchaser_id = purch.user_id
            LEFT JOIN users subcategorymanager on p.category_manager_id = subcategorymanager.user_id
            INNER JOIN users u on u.user_id = tmp.user_id
            INNER JOIN user_comms uc on u.user_id = uc.user_id and uc.main = 1";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function usersPerCommunity($params)
    {
        $sql =
            'SELECT /*+ MAX_EXECUTION_TIME(900000) */ c.name, count(*) FROM `users` u inner join subscriptions s on s.user_id = u.user_id left join community_representative_group c on c.community_representative_group_id = u.community_representative_group_id WHERE s.active = 1 group by c.community_representative_group_id';

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function missputReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ tab.*,
                CONCAT(u.first_name,' ',u.last_name,' (',u.user_id,')') AS 'Packer',
                o.delivery_date as 'Delivery Date'
            FROM
                (
                SELECT
                    refunds.*,
                    ttp.task_id,
                    ttp.status as 'Task Status',
                    ttp.position_on_cart,
                    ttp.reserved_by,
                    od.refund_comment
                FROM
                    task_type_prepbasket ttp
                INNER JOIN(
                    SELECT DATE(created_time) AS DATE,
                        transaction_id as 'Transaction ID',
                        SUM(amount) AS 'Refund Amount',
                        COUNT(DISTINCT transaction_id) AS COUNT,
                        order_id,
                        product_id,
                        type,
                        order_details_id,
                        description
                    FROM
                        transactions
                    WHERE type = 34 AND date(created_time) BETWEEN :start AND :end AND transactions.child_transaction_id = 0
                    GROUP BY
                        order_id,
                        product_id
                ) AS refunds
            ON
                (refunds.order_id = ttp.order_id AND ttp.product_id = refunds.product_id AND ttp.status != 2)
            LEFT JOIN order_details od ON
                od.order_details_id = refunds.order_details_id) tab
            LEFT JOIN orders o ON
                o.order_id = tab.order_id
            INNER JOIN task t ON
                t.task_id = tab.task_id AND t.status = 'Done'
            LEFT JOIN products p ON
                p.product_id = tab.product_id
            LEFT JOIN ww_orders wwo ON
                wwo.order_number = tab.order_id
            LEFT JOIN ww_steps wws ON
                wws.order_id = wwo.order_id AND wws.type = 0
            LEFT JOIN ww_routes wwr ON
                wwr.route_id = wwo.route_id
            LEFT JOIN users drivers ON
                wws.user_id = drivers.user_id
            LEFT JOIN users u ON
                u.user_id = tab.reserved_by
            GROUP BY
                tab.order_id,
                tab.product_id,
                tab.position_on_cart";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function allInventoryActivities7days($params)
    {
        if (empty($params['end'])) {
            throw new Exception('Missing date params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ YEARWEEK(ia.`timestamp`, 6) AS 'Week #',
                iat.name,
                ia.`timestamp`,
                i.`product_id` as 'product id',
                s.`name` AS 'supplier name',
                p.`name` 'product name',
                if(p.charge_by_weight, 'yes', 'no') as 'charge by weight item',
                ia.`lot_id`,
                i.`created`,
                i.`Expiry_date`,
                datediff(ia.`timestamp`, i.`created`) AS 'Total number of days IN inventory',
                SUM(ia.`quantity`) * if(ia.`inventory_activity_type_id` / 100 = 1, -1, 1) AS 'Quantity',
                IF(ia.inventory_activity_type_id = 100,'Not available, PICKING is all WH employees together' ,CONCAT(iu.`first_name`, ' ', iu.`last_name`)) AS 'Name of person who did inventory activity',
                IF(ia.inventory_activity_type_id = 100,'Not available, PICKING is all WH employees together' ,iu.internal_grouping_code) AS 'IGC Code of person who did inventory activity',
                IFNULL(ttic.reason_id, 'Manual') as source,
                REPLACE(ia.`Comment`,'\r\n',' ') as Comment, 
                CONCAT(pu.`first_name`, ' ', pu.`last_name`) AS Purchaser,
                CONCAT(pu1.`first_name`, ' ', pu1.`last_name`) AS 'Category Manager',
                p.`purchasing_price`, 
                p.`price`,
                p.`purchasing_price` * ia.`quantity` * if(ia.`inventory_activity_type_id` / 100 = 1, -1, 1) AS 'Total VALUE of ALL removals AT COST',
                p.`price` * ia.`quantity` * if(ia.`inventory_activity_type_id` / 100 = 1, -1, 1) AS 'Total VALUE of ALL removals AT RETAIL VALUE'
            FROM
                `inventory_activity` ia
            LEFT JOIN inventory i ON i.lot_id = ia.lot_id
            LEFT JOIN supplier_forecast_orders sfo ON sfo.supplier_forecast_orders_id = i.supplier_forecast_orders_id
            LEFT JOIN products p ON p.product_id = sfo.product_id
            LEFT JOIN suppliers s ON s.supplier_id = p.supplier_id
            LEFT JOIN inventory_activity_types iat ON iat.inventory_activity_type_id = ia.inventory_activity_type_id
            LEFT JOIN users iu ON iu.user_id = ia.user_id
            LEFT JOIN users pu ON pu.user_id = p.purchaser_id
            LEFT JOIN users pu1 ON pu.user_id = p.category_manager_id
            LEFT JOIN
                task_type_inventorycheck ttic ON ttic.task_type_inventorycheck_id = ia.task_type_inventorycheck_id
            WHERE
                ia.`inventory_activity_type_id` < 300 AND
                DATE(ia.`TIMESTAMP`) BETWEEN :end AND  ADDDATE(:end, INTERVAL 7 DAY)
            GROUP BY IF(ia.inventory_activity_type_id = 100, DATE(ia.timestamp), ia.inventory_activity_id), IF(ia.inventory_activity_type_id = 100, CONCAT('LOT:',ia.lot_id), ia.inventory_activity_id)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':end', $params['end'])->queryAll();
    }

    public static function usesOfCompletedCouponCode($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ COUNT(*) AS 'nb users who used completed', 
            SUM(amount) AS 'total credits given' 
        FROM 
            `transactions` 
        WHERE 
            DATE(created_time) BETWEEN :start AND :end AND 
            coupon_id = 108846";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function cancellationStats($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(c.created) AS 'Date',
                SUM(IF(s.user_id != c.staff_id, 1, 0)) AS 'CS - Cancellation tickets',
                SUM(IF(s.user_id != c.staff_id AND c.retained = 0, 1, 0)) AS 'CS - Churn',
                ROUND((SUM(IF(s.user_id != c.staff_id AND c.retained = 1, 1, 0))/SUM(IF(s.user_id != c.staff_id, 1, 0)))*100,2) AS 'CS - Retention rate',
                selfcancelsteps.clicks AS 'SELF - Cancellation button clicks',
                selfcancelsteps.step_1 AS 'SELF - Step 1',
                selfcancelsteps.step_2 AS 'SELF - Step 2',
                selfcancelsteps.step_3 AS 'SELF - Step 3',
                selfcancelsteps.step_4 AS 'SELF - Step 4',
                ROUND((SUM(IF(s.user_id = c.staff_id AND c.retained = 1, 1, 0))/selfcancelsteps.clicks)*100,2) AS 'SELF - Retention rate (full path)',
                ROUND((1-(SUM(IF(s.user_id = c.staff_id AND c.retained = 0, 1, 0))/selfcancelsteps.step_1))*100,2) AS 'SELF - Retention rate (partial path)'
            FROM
                `cancelation` c
            INNER JOIN subscriptions s ON (s.subscription_id = c.subscription_id)
            LEFT JOIN (
                SELECT
                    d,
                    SUM(IF(slide IS NULL, 1, 0)) AS 'clicks',
                    SUM(IF(slide = 0, 1, 0)) AS 'step_1',
                    SUM(IF(slide IN (1,2,3,4), 1, 0)) AS 'step_2',
                    SUM(IF(slide = 5, 1, 0)) AS 'step_3',
                    SUM(IF(slide = 8, 1, 0)) AS 'step_4'
                FROM (
                    SELECT
                        user_id,
                        subscription_id,
                        DATE(created_at) AS d,
                        slide
                    FROM
                        `cancelation_flow_action_logs`
                    GROUP BY
                        user_id,
                        DATE(created_at),
                        slide
                ) steps
                GROUP BY
                    d
            ) as selfcancelsteps ON (selfcancelsteps.d = DATE(c.created))
            WHERE
                DATE(c.created) >= '2021-02-07'
            GROUP BY
                DATE(c.created)";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function cancellationLogs($params)
    {
        $sql =
            "SELECT
                    /*+ MAX_EXECUTION_TIME(900000) */ DATE(c.created) AS 'Cancellation Date',
                    c.created AS 'Cancellation timestamp',
                    IF(s.user_id != c.staff_id, 'By CS (CS Tool)', 'By Customer (account settings)') AS 'Cancellation flow',
                    IF(c.retained = 1, 'No (voided)', 'Yes') AS 'Canceled successfully',
                    uc.name AS 'Customer Name',
                    uc.phone AS 'Customer Phone',
                    uc.email AS 'Customer Email',
                    uc.lang AS 'Customer Language',
                    DATE(s.created) AS 'Account created on',
                    ROUND(IFNULL(sales.amount, 0),2) AS 'lifetime total order amount ($)',
                    u.zip_code AS 'Postal Code',
                    cr.reason AS 'Cancellation reason',
                    c.note AS 'Cancellation note'
                FROM
                    `cancelation` c
                INNER JOIN subscriptions s ON (s.subscription_id = c.subscription_id)
                LEFT JOIN user_comms uc ON (uc.user_id = s.user_id AND uc.main = 1 AND uc.in_use = 1)
                LEFT JOIN users u ON (u.user_id = s.user_id)
                LEFT JOIN cancelation_reasons cr ON (cr.id = c.reason)
                LEFT JOIN (
                    SELECT 
                        user_id,
                        SUM(total_order_amount) AS amount
                    FROM 
                        orders
                    WHERE 
                        status = 4
                    GROUP BY 
                        user_id
                ) sales ON (sales.user_id = s.user_id)
                WHERE
                    DATE(c.created) >= '2021-02-07'
                ORDER BY 
                    c.created
                ";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function productsDimensions($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ pc.name AS 'Category',
                p.product_id AS 'Product ID',
                p.name AS 'Product Name',
                CONCAT(u.first_name, ' ', u.last_name) AS 'Purchaser',
                CONCAT(u1.first_name, ' ', u1.last_name) AS 'Category Manager',
                CONCAT('L:', p.length, ' W:', p.width, ' H:', p.height) AS 'Current Dimensions (L,W,H)',
                p.volume AS 'Current Volume',
                p.weight AS 'Current Weight',
                p.product_weight_with_packaging AS 'Current Weight w/ Packaging',
                /*DATE(arl.created_at) AS 'Weight /w Packaging Last Updated On'*/
                DATE(p.updated) AS 'Product Last Updated On'
            FROM
                products p
            INNER JOIN productSubSubCategories pssc ON
                (
                p.sub_sub_id = pssc.sub_sub_id
                )
            INNER JOIN users u ON
                (
                u.user_id = p.purchaser_id
                )
            INNER JOIN users u1 ON
                (
                u1.user_id = p.category_manager_id
                )
            INNER JOIN productSubCategories psc ON
                (
                pssc.subcategory_id = psc.subcategory_id
                )
            INNER JOIN product_categories pc ON 
                (
                psc.category_id = pc.category_id
                )
            /*INNER JOIN securelufacoms.active_record_log arl ON 
                (
                arl.model_id = p.product_id AND model = 'Products' AND action = 'CHANGE'
                )*/";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function numberOfUnitsInInventoryPerProduct($params)
    {
        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ i.product_id AS 'Product ID',
                    p.name AS 'Product Name (FR)',
                    pl.l_name AS 'Product Name (EN)',
                    CASE
                        WHEN p.conservation_mode_in_warehouse = 0 THEN 'WAREHOUSE_CONSERVATION_AMBIENT'
                        WHEN p.conservation_mode_in_warehouse = 1 THEN 'WAREHOUSE_CONSERVATION_REFRIGERATED_4_DEGREES'
                        WHEN p.conservation_mode_in_warehouse = 2 THEN 'WAREHOUSE_CONSERVATION_REFRIGERATED_15_DEGREES'
                        WHEN p.conservation_mode_in_warehouse = 3 THEN 'WAREHOUSE_CONSERVATION_FROZEN'
                        WHEN p.conservation_mode_in_warehouse = 4 THEN 'WAREHOUSE_CONSERVATION_OFFSITE_REFRIGERATED_4_DEGREES'
                        WHEN p.conservation_mode_in_warehouse = 5 THEN 'WAREHOUSE_CONSERVATION_OFFSITE_FROZEN'
                        WHEN p.conservation_mode_in_warehouse = 6 THEN 'WAREHOUSE_CONSERVATION_REFRIGERATED_8_DEGREES'
                        ELSE 'UNKNOWN - WEIRD'
                    END AS 'Conservation mode in warehouse',
                    p.volume AS 'Product Volume (cc)',
                    p.weight AS 'Product weight',
                    p.product_weight_with_packaging AS 'Product weight with packaging',
                    SUM(i.number_of_units_available) AS 'Number of units in inventory',
                    ROUND(SUM(i.number_of_units_available)*p.product_weight_with_packaging,2) AS 'Total volume (cc)'
                FROM 
                    `inventory` i 
                INNER JOIN products p ON (p.product_id = i.product_id)
                INNER JOIN productsLang pl ON (pl.product_id = i.product_id AND pl.lang_id = 'en')
                WHERE 
                    i.number_of_units_available>0
                GROUP BY 
                    i.product_id";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function packingErrorReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        return RootDashboard::getErrorRate('', $params['start'], $params['end'], false, false, true);
    }

    public static function pupsAllOrders($params)
    {
        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ d.name AS 'Droppoint Name',
                    orders.droppoint_id AS 'Droppoint ID',
                    d.address AS 'Droppoint address',
                    d.zip_code AS 'Droppoint zipcode',
                    d.published AS 'Droppoint published ? (0 no, 1 yes)',
                    COUNT(orders.order_id) AS '# orders shipped',  
                    MIN(orders.delivery_date) AS 'Min shipping date',
                    MAX(orders.delivery_date) AS 'Max shipping date'
                FROM 
                    `orders`
                INNER JOIN droppoints d ON (d.droppoint_id = orders.droppoint_id)
                WHERE 
                    orders.status = 4
                GROUP BY 
                    orders.droppoint_id;";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function ordersAndBoxesPerWeekForPUPAndHD($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ WEEK(o.delivery_date, 6) AS 'Week #',
                COUNT(o.order_id) AS 'All - # orders',
                SUM(o.number_box_needed) AS 'All - # boxes',
                ROUND(SUM(o.number_box_needed) / COUNT(o.order_id),2) AS 'All - avg boxes',
                SUM(IF(`delivery_service_amount`>0, 1, 0)) AS 'HD - # orders',
                SUM(IF(`delivery_service_amount`>0, o.number_box_needed, 0)) AS 'HD - # boxes',
                ROUND(SUM(IF(`delivery_service_amount`>0, o.number_box_needed, 0)) / SUM(IF(`delivery_service_amount`>0, 1, 0)),2) AS 'HD - avg boxes',
                SUM(IF(`delivery_service_amount`<=0, 1, 0)) AS 'PUP/STA - # orders',
                SUM(IF(`delivery_service_amount`<=0, o.number_box_needed, 0)) AS 'PUP/STA - # boxes',
                ROUND(SUM(IF(`delivery_service_amount`<=0, o.number_box_needed, 0)) / SUM(IF(`delivery_service_amount`<=0, 1, 0)),2) AS 'PUP/STA - avg boxes'
            FROM 
                `orders` o
            WHERE 
                o.status = 4 AND
                o.delivery_date BETWEEN :start AND :end
            GROUP BY 
                WEEK(o.delivery_date, 6)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function creditsGivenToPUPCoordinatorsPerWeek($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $params['start'] = "{$params['start']} 00:00:00";
        $params['end'] = "{$params['end']} 23:59:59";

        $sql = <<<_EOQ
SELECT 
    /*+ MAX_EXECUTION_TIME(900000) */
    DATE(t.created_time) AS 'Date',
    IFNULL(staff.user_email, '-') AS 'User who adjusted the credits',
    d_infos.pup_ids AS 'PUP IDs',
    d_infos.pup_names AS 'PUP names',
    t.user_id AS 'PUP coordinator ID',
    CONCAT(coord.first_name, ' ', coord.last_name) AS 'PUP coordinator name',
    SUM(IF(t.type = 13, amount, 0)) AS 'Adjustment - Added credits (Partners) - 13',
    SUM(IF(t.type = 26, amount, 0)) AS 'Reset credits (Lufa Partners) - 26',
    SUM(IF(t.type = 3, amount, 0)) AS 'Remove credits expiration - 3'
FROM
    transactions t
LEFT JOIN users coord ON (coord.user_id = t.user_id)
LEFT JOIN (
    SELECT 
        dc.user_id,
        GROUP_CONCAT(DISTINCT d.droppoint_id) pup_ids,
        GROUP_CONCAT(DISTINCT d.name) pup_names
    FROM
        droppoints_coordinators dc
    INNER JOIN droppoints d ON (dc.droppoints_id = d.droppoint_id)
    WHERE 
        dc.primary_coordinator = 1
    GROUP BY 
        dc.user_id
) d_infos ON (d_infos.user_id = coord.user_id)
LEFT JOIN users staff ON (t.staff_id = staff.user_id)
WHERE 
    t.created_time BETWEEN :start AND :end AND
    t.type IN (13, 26, 3) AND 
    t.finalized = 1
GROUP BY 
    DATE(t.created_time),
    staff.user_email,
    coord.user_id
_EOQ;

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function pupCreditTransactions($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $params['start'] = "{$params['start']} 00:00:00";
        $params['end'] = "{$params['end']} 23:59:59";

        $sql = <<<_EOQ
SELECT 
    o.droppoint_id AS 'Droppoint ID',
    d.name AS 'Droppoint name',
    IF(d.auto_deposit = 1, 'Yes', 'No') AS 'Auto deposit is active',
    IF(d.hd_product_id IS NOT NULL, 'HD', 'PUP') AS 'Droppoint type',
    COUNT(o.order_id) AS '# orders shipped',
    u.user_id AS 'User ID of PUP coordinator',
    u.user_email AS 'Email of PUP coordinator',
    dc.deposit_amount AS 'Deposit amount per order',
    IFNULL(tr.`Adjustment - Added credits (Partners) - 13`, 0) AS `Adjustment - Added credits (Partners) - 13`,
    IFNULL(tr.`Reset credits (Lufa Partners) - 26`, 0) AS `Reset credits (Lufa Partners) - 26`,
    IFNULL(tr.`Remove credits expiration - 3`, 0) AS `Remove credits expiration - 3`
FROM 
        orders o 
INNER JOIN droppoints d ON (d.droppoint_id = o.droppoint_id)
LEFT JOIN droppoints_coordinators dc ON (dc.droppoints_id = o.droppoint_id AND dc.primary_coordinator = 1)
LEFT JOIN users u ON (u.user_id = dc.user_id)
LEFT JOIN (
    SELECT 
            t.user_id,
            SUM(IF(t.type = 13, t.amount, 0)) AS `Adjustment - Added credits (Partners) - 13`,
            SUM(IF(t.type = 26, t.amount, 0)) AS `Reset credits (Lufa Partners) - 26`,
            SUM(IF(t.type = 3, t.amount, 0)) AS `Remove credits expiration - 3`
    FROM 
            transactions t 
    WHERE 
            t.user_id IN (SELECT DISTINCT user_id FROM droppoints_coordinators) AND 
            t.type IN (13, 26, 3) AND 
            t.created_time BETWEEN :start AND :end AND 
            t.finalized = 1
    GROUP BY 
            t.user_id
) tr ON (tr.user_id = dc.user_id)
WHERE 
        o.status = 4 AND
    o.delivery_date BETWEEN :start AND :end
GROUP BY 
        o.droppoint_id  
ORDER BY `tr`.`Remove credits expiration - 3`  DESC
_EOQ;

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function giftCertificatePurchasedPerDay($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(created) 'date',
                COUNT(*) 'number of gift certificates',
                SUM(amount) 'total gifted'
            FROM coupons
            WHERE
                gift_certificate = 1
                AND DATE(created) BETWEEN :start AND :end
            GROUP BY 
                DATE(created)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function superLufavoreCount($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ COUNT(*) AS '# Super Lufavore'
            FROM
                securelufacom.supervores_incentive_log si
            INNER JOIN securelufacom.subscriptions s ON (s.user_id = si.user_id)
            WHERE
                si.active = 1 AND supervores_incentive_log_id IN(
                SELECT
                    MAX(supervores_incentive_log_id)
                FROM
                    securelufacom.supervores_incentive_log
                GROUP BY
                    user_id
            ) AND s.active = 1 AND s.type = 0";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function superGivebackRateBreakdown($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ pct AS 'pct', 
                COUNT(*) AS 'count'
            FROM
                securelufacom.supervores_incentive_log si
            INNER JOIN securelufacom.subscriptions s ON (s.user_id = si.user_id)
            WHERE
                si.active = 1 AND supervores_incentive_log_id IN (
                SELECT
                    MAX(supervores_incentive_log_id)
                FROM
                    securelufacom.supervores_incentive_log
                GROUP BY
                    user_id
            ) AND s.active = 1 AND s.type = 0
            GROUP BY
                pct";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function takeRateAfterGoingSuperLufavore($params)
    {
        if (empty($params['weeks'])) {
            throw new Exception('Missing weeks number params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ ROUND(SUM(tmp.processed_order_lufa) / SUM(tmp.count_lufa) * 100, 2) as 'Take Rate Lufavore',
                ROUND(SUM(tmp.processed_order_super) / SUM(tmp.count_super) * 100, 2) as 'Take Rate SuperLufavore'
            FROM ( 
                SELECT 
                        s.user_id,
                        s.became_superlufavore_on,
                        IF(o.subscription_type = 0, IF(o.status = 4, 1, 0), 0) as processed_order_super,
                        IF(o.subscription_type = 1, IF(o.status = 4, 1, 0), 0) as processed_order_lufa,
                        IF(o.subscription_type = 0, 0, 1) as count_lufa,
                        IF(o.subscription_type = 0, 1, 0) as count_super
                FROM 
                    orders o
                INNER JOIN
                    subscriptions s ON s.user_id = o.user_id AND s.active = 1 AND s.type = 0
                WHERE 
                    o.delivery_date BETWEEN SUBDATE(s.became_superlufavore_on, INTERVAL :weeks WEEK) AND 					
                    ADDDATE(s.became_superlufavore_on, INTERVAL :weeks WEEK)
                GROUP BY 
                    s.user_id, WEEK(o.delivery_date, 3)
            ) tmp";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':weeks', $params['weeks'])->queryAll();
    }

    public static function competition_pricing_report()
    {
    $sql = "
        SELECT
            IF(p.status = 1, 'Yes', 'No') AS 'Published?',
            CONCAT(m.first_name, ' ', m.last_name) AS 'Category Manager',
            CONCAT(su.first_name, ' ', su.last_name) AS 'Purchaser',
            pcl.l_name AS 'Category',    
            pscl.l_name AS 'Sub-Category',
            p.product_id AS 'ID',
            pl.l_name AS 'Product name',
            s.name AS 'Supplier',
            p.weight AS 'Format',
            qs AS 'Annual Sales (Qty)',
            p.purchasing_price AS 'Unit Cost',    
            p.price AS 'Regular Retail',    
            ROUND(p.price / p.weight * 100, 2) AS 'Regular Retail / 100g,ml',    
            ROUND((p.price - p.purchasing_price) / p.price, 2) AS 'Regular Margin %',
            ROUND((p.price - p.purchasing_price), 2) AS 'Regular Margin $'
        
        FROM 
            products p
            INNER JOIN productSubSubCategories AS pssc ON pssc.sub_sub_id = p.sub_sub_id
            INNER JOIN productSubCategories AS psc ON pssc.subcategory_id = psc.subcategory_id
            INNER JOIN product_categories AS pc ON psc.category_id = pc.category_id
            INNER JOIN suppliers AS s ON p.supplier_id = s.supplier_id
            LEFT JOIN users AS su ON p.purchaser_id = su.user_id
            LEFT JOIN users AS m ON p.category_manager_id = m.user_id
            JOIN productCategoriesLang pcl ON pc.category_id = pcl.category_id
            JOIN productSubCategoriesLang pscl ON psc.subcategory_id = pscl.subcategory_id
            JOIN productsLang pl ON p.product_id = pl.product_id
            LEFT JOIN (
                SELECT 
                    product_id,
                    SUM(qty_sold) AS 'qs'
                FROM 
                    product_stats
                WHERE 
                    date BETWEEN DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND CURDATE()
                GROUP BY 
                    product_id
            ) ps ON p.product_id = ps.product_id
            
        GROUP BY 
            p.product_id
        ORDER BY 
            p.product_id ASC";

    return Yii::app()->rodb->createCommand($sql)->queryAll();
    }



    public static function abpAfterGoingSuperLufavore($params)
    {
        if (empty($params['weeks'])) {
            throw new Exception('Missing weeks number params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ ROUND(SUM(tmp.total_lufa) / SUM(tmp.count_lufa), 2) as 'ABP Lufavore',
                ROUND(SUM(tmp.total_super) / SUM(tmp.count_super), 2) 'ABP SuperLufavore'
            FROM (
                SELECT 
                        s.user_id,
                        s.became_superlufavore_on,
                        MIN(o.delivery_date),
                        MAX(o.delivery_date),
                        SUM(IF(o.subscription_type = 0, 0, 1)) as count_lufa,
                        ROUND(SUM(IF(o.subscription_type = 0, 0, o.total_order_amount)),2) as total_lufa,
                        SUM(IF(o.subscription_type = 0, 1, 0)) as count_super,
                        ROUND(SUM(IF(o.subscription_type = 0,  o.total_order_amount, 0)),2) as total_super
                FROM 
                    orders o
                INNER JOIN
                    subscriptions s ON s.user_id = o.user_id AND s.active = 1 AND s.type = 0
                WHERE 
                    o.delivery_date BETWEEN SUBDATE(s.became_superlufavore_on, INTERVAL :weeks WEEK) AND 					
                    ADDDATE(s.became_superlufavore_on, INTERVAL :weeks WEEK) AND 
                    o.status = 4
                GROUP BY 
                    s.user_id
            ) tmp";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':weeks', $params['weeks'])->queryAll();
    }
    /*public static function taskPutsPerZone($params)
    {
        $output = [];
        $meta_sql =
        "SELECT
            t.task_id,
            date(t.finished_at) AS `Date`,
            u.internal_employee_id AS 'Packer ID (employeurD)'
        FROM
            task t
        RIGHT JOIN task_type_prepbasket ttpb ON t.task_id = ttpb.task_id
        LEFT JOIN users u ON u.user_id = t.assigned_to
        WHERE
            date(t.finished_at) BETWEEN :start AND :end
        ";
        $meta_data = AppGlobal::arrayReduceWithKey(Yii::app()->rodb->createCommand($meta_sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll(), ['task_id']) ;

        foreach(array_keys($meta_data) as $t_id){
            $put_data = Yii::app()->rodb->createCommand(
            "SELECT
                puts.task_id,
                puts.updated_at,
                unix_timestamp(puts.updated_at) AS `Unix`,
                o.number_box_needed AS `Boxes`,
                IF(p.conservation_mode_in_warehouse = 1, '4_deg', IF(p.conservation_mode_in_warehouse = 0, 'ambient', IF(p.conservation_mode_in_warehouse = 2, '15_deg', IF(p.conservation_mode_in_warehouse = 3 AND p.freezer_storage = 2, 'freezer_chest', IF(p.conservation_mode_in_warehouse = 3 AND p.freezer_storage = 1, 'freezer_walkin', p.conservation_mode_in_warehouse))))) AS `Zone`,
                IF(p.conservation_mode_in_basket = 1 OR p.conservation_mode_in_basket = 2, 1, 0) AS `Biso Put`,
                unix_timestamp(puts.updated_at) - unix_timestamp(LAG(puts.updated_at) OVER(ORDER BY puts.updated_at)) AS put_length
            FROM
                task_type_prepbasket puts
            LEFT JOIN task t ON puts.task_id = t.task_id
            LEFT JOIN orders o ON o.order_id = puts.order_id
            LEFT JOIN products p ON puts.product_id = p.product_id
            WHERE puts.task_id = {$t_id}
            AND puts.status = 1
            ORDER BY puts.updated_at ASC
            ")->queryAll();
            // var_dump($t_id);
            // var_dump($put_data);
            // die();
            $output_row = [
            'Task' => $t_id,
            'Date' => $meta_data[$t_id]['Date'],
            'Packer ID (employeurD)' => $meta_data[$t_id]['Packer ID (employeurD)'],

            // 'No. of baskets' => $put_data['Boxes'],
            'Total Puts' => count($put_data),
            'Total Time (in sec)' => 0,
            'Efficiency per cart (sec/put)' => 0,

            'Biso Puts' => 0,
            'Total Biso Time (secs)' => 0,

            'Chest Freezer Puts (Zone 000)' => 0,
            'Chest Freezer Time (secs)' => 0,
            'Chest Freezer Efficiency (sec/put)' => 0,

            'Freezer Puts (Zone 100)' => 0,
            'Freezer Time (secs)' => 0,
            'Freezer Efficiency (sec/put)' => 0,

            '4 degrees Puts (Zone 200)' => 0,
            '4 degrees Time (secs)' => 0,
            '4 degrees Efficiency (sec/put)' => 0,

            '15 degrees Puts (Zone 400)' => 0,
            '15 degrees Time (secs)' => 0,
            '15 degrees Efficiency (sec/put)' => 0,

            'Dry Puts (Zone 500)' => 0,
            'Dry Time (secs)' => 0,
            'Dry Efficiency (sec/put)' => 0,

            'Other Puts (misc.)' => 0,
            'Other Puts Time (secs)' => 0,
            'Other Puts Effeciency (sec/put)' => 0,
            ];

            foreach($put_data as $row) {
            if($row['Biso Put'] == 1){
                $output_row['Biso Puts'] += 1;
                $output_row['Total Biso Time (secs)'] += $row['put_length'];
            }
            $output_row['Total Time (in sec)'] += intval($row['put_length']);

            switch ($row['Zone']) {
                case '4_deg':
                $output_row['4 degrees Puts (Zone 200)'] += 1;
                $output_row['4 degrees Time (secs)'] += intval($row['put_length']);
                break;
                case 'ambient':
                $output_row['Dry Puts (Zone 500)'] += 1;
                $output_row['Dry Time (secs)'] += intval($row['put_length']);
                break;
                case '15_deg':
                $output_row['15 degrees Puts (Zone 400)'] += 1;
                $output_row['15 degrees Time (secs)'] += intval($row['put_length']);
                break;
                case 'freezer_chest':
                $output_row['Chest Freezer Puts (Zone 000)'] += 1;
                $output_row['Chest Freezer Time (secs)'] += intval($row['put_length']);
                break;
                case 'freezer_walkin':
                $output_row['Freezer Puts (Zone 100)'] += 1;
                $output_row['Freezer Time (secs)'] += intval($row['put_length']);
                break;
                default:
                $output_row['Other Puts (misc.)'] += 1;
                $output_row['Other Puts Time (secs)'] += intval($row['put_length']);
                break;
            }
            }
            $output_row['Efficiency per cart (sec/put)'] = $output_row['Total Puts'] > 0 ? round($output_row['Total Time (in sec)'] / $output_row['Total Puts']) : null;
            $output_row['4 degrees Efficiency (sec/put)'] = $output_row['4 degrees Puts (Zone 200)'] > 0 ? round($output_row['4 degrees Time (secs)'] / $output_row['4 degrees Puts (Zone 200)']) : null;
            $output_row['15 degrees Efficiency (sec/put)'] = $output_row['15 degrees Puts (Zone 400)'] > 0 ? round($output_row['15 degrees Time (secs)'] / $output_row['15 degrees Puts (Zone 400)']) : null;
            $output_row['Dry Efficiency (sec/put)'] = $output_row['Dry Puts (Zone 500)'] > 0 ? round($output_row['Dry Time (secs)'] / $output_row['Dry Puts (Zone 500)']) : null;
            $output_row['Chest Freezer Efficiency (sec/put)'] = $output_row['Chest Freezer Puts (Zone 000)'] > 0 ? round($output_row['Chest Freezer Time (secs)'] / $output_row['Chest Freezer Puts (Zone 000)']): null ;
            $output_row['Freezer Efficiency (sec/put)'] = $output_row['Freezer Puts (Zone 100)'] > 0 ? round($output_row['Freezer Time (secs)'] / $output_row['Freezer Puts (Zone 100)']) : null;
            $output_row['Other Puts Effeciency (sec/put)'] = $output_row['Other Puts (misc.)'] > 0 ? round($output_row['Other Puts Time (secs)'] / $output_row['Other Puts (misc.)']) : null;

            $output[] = $output_row;
        }
        //   var_dump($output);
        return $output;
    }*/

    public static function taskPutsPerZone($params)
    {
        $output = [];
        $meta_sql =
        "SELECT
            /*+ MAX_EXECUTION_TIME(900000) */ t.task_id,
            date(t.finished_at) AS `Date`,
            u.internal_employee_id AS 'Packer ID (employeurD)' 
        FROM
            task t 
        LEFT JOIN users u ON u.user_id = t.assigned_to
        WHERE
            t.finished_at BETWEEN :start AND :end AND 
            t.status = :STATUS_DONE AND 
            t.task_type_defaults_id = 3
        GROUP BY t.task_id
        ";
        $meta_data = AppGlobal::arrayReduceWithKey(Yii::app()->rodb->createCommand($meta_sql)->bindValues(
            [
                ':start' => $params['start']." 00:00:00",
                ':end' => $params['end']." 23:59:59",
                ':STATUS_DONE' => Task::STATUS_DONE,
            ])->queryAll(), ['task_id']);

        foreach (array_keys($meta_data) as $t_id) {
            $put_data = Yii::app()->rodb->createCommand(
                "SELECT
                    /*+ MAX_EXECUTION_TIME(900000) */ puts.task_id,
                    puts.updated_at,
                    unix_timestamp(puts.updated_at) AS `Unix`,
                    pa.lot_id,
                    lh.conservation_mode_in_warehouse AS `Zone`,
                    IF(p.conservation_mode_in_basket = 1 OR p.conservation_mode_in_basket = 2, 1, 0) AS `Biso Put`,
                    unix_timestamp(puts.updated_at) - unix_timestamp(LAG(puts.updated_at) OVER(ORDER BY puts.updated_at)) AS put_length
                FROM 
                    task_type_prepbasket puts
                LEFT JOIN pick_activities pa ON puts.task_type_prepbasket_id = pa.task_type_prepbasket_id
                LEFT JOIN products p ON puts.product_id = p.product_id
                LEFT JOIN location_header lh ON pa.lot_location_header_id = lh.location_header_id
                WHERE puts.task_id = {$t_id}
                AND puts.status = 1
                ORDER BY puts.updated_at ASC
            "
            )->queryAll();

            $output_row = [
                'Task' => $t_id,
                'Date' => $meta_data[$t_id]['Date'],
                'Packer ID (employeurD)' => $meta_data[$t_id]['Packer ID (employeurD)'],

                // 'No. of baskets' => $put_data['Boxes'],
                'Total Puts' => count($put_data),
                'Total Time (in sec)' => 0,
                'Efficiency per cart (sec/put)' => 0,

                'Biso Puts' => 0,
                'Total Biso Time (secs)' => 0,

                'Freezer Puts (Zone 100)' => 0,
                'Freezer Time (secs)' => 0,
                'Freezer Efficiency (sec/put)' => 0,

                '4 degrees Puts (Zone 200)' => 0,
                '4 degrees Time (secs)' => 0,
                '4 degrees Efficiency (sec/put)' => 0,

                '15 degrees Puts (Zone 300)' => 0,
                '15 degrees Time (secs)' => 0,
                '15 degrees Efficiency (sec/put)' => 0,

                'Dry Puts (Zone 000 & Zone 400)' => 0,
                'Dry Time (secs)' => 0,
                'Dry Efficiency (sec/put)' => 0,

                'Other Puts (misc.)' => 0,
            ];

            foreach ($put_data as $row) {
                if ($row['Biso Put'] == 1) {
                    $output_row['Biso Puts'] += 1;
                    $output_row['Total Biso Time (secs)'] += $row['put_length'];
                }
                $output_row['Total Time (in sec)'] += intval($row['put_length']);
                if ($row['Zone'] != null) {
                    switch ($row['Zone']) {
                        case 0: // WAREHOUSE_CONSERVATION_AMBIENT
                            $output_row['Dry Puts (Zone 000 & Zone 400)'] += 1;
                            $output_row['Dry Time (secs)'] += intval($row['put_length']);
                            break;
                        case 1: // WAREHOUSE_CONSERVATION_REFRIGERATED_4_DEGREES
                            $output_row['4 degrees Puts (Zone 200)'] += 1;
                            $output_row['4 degrees Time (secs)'] += intval($row['put_length']);
                            break;
                        case 2: // WAREHOUSE_CONSERVATION_REFRIGERATED_15_DEGREES
                        case 4: // WAREHOUSE_CONSERVATION_OFFSITE_REFRIGERATED_4_DEGREES
                            $output_row['15 degrees Puts (Zone 300)'] += 1;
                            $output_row['15 degrees Time (secs)'] += intval($row['put_length']);
                            break;
                        case 3: // WAREHOUSE_CONSERVATION_FROZEN
                        case 5: // WAREHOUSE_CONSERVATION_OFFSITE_FROZEN
                            $output_row['Freezer Puts (Zone 100)'] += 1;
                            $output_row['Freezer Time (secs)'] += intval($row['put_length']);
                            break;
                        default:
                            $output_row['Other Puts (misc.)'] += 1;
                            break;
                    }
                } else {
                    $output_row['Other Puts (misc.)'] += 1;
                }
            }
            $output_row['Efficiency per cart (sec/put)'] = $output_row['Total Puts'] > 0 ? round($output_row['Total Time (in sec)'] / $output_row['Total Puts']) : null;
            $output_row['4 degrees Efficiency (sec/put)'] = $output_row['4 degrees Puts (Zone 200)'] > 0 ? round($output_row['4 degrees Time (secs)'] / $output_row['4 degrees Puts (Zone 200)']) : null;
            $output_row['15 degrees Efficiency (sec/put)'] = $output_row['15 degrees Puts (Zone 300)'] > 0 ? round($output_row['15 degrees Time (secs)'] / $output_row['15 degrees Puts (Zone 300)']) : null;
            $output_row['Dry Efficiency (sec/put)'] = $output_row['Dry Puts (Zone 000 & Zone 400)'] > 0 ? round($output_row['Dry Time (secs)'] / $output_row['Dry Puts (Zone 000 & Zone 400)']) : null;
            $output_row['Freezer Efficiency (sec/put)'] = $output_row['Freezer Puts (Zone 100)'] > 0 ? round($output_row['Freezer Time (secs)'] / $output_row['Freezer Puts (Zone 100)']) : null;
            // $output_row['Other Puts Effeciency (sec/put)'] = $output_row['Other Puts (misc.)'] > 0 ? round($output_row['Other Puts Time (secs)'] / $output_row['Other Puts (misc.)']) : null;

            $output[] = $output_row;
        }
        return $output;
    }

    public static function automatedRefundsReceptionShortsQualityDidntMeetOurStandardPerWeek($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ WEEK(t.created_time, 6) AS 'Week #',
                SUM(t.amount) AS 'refunds amount (reception - shorts + quality didn t meet our standard)'
            FROM
                transactions t
            WHERE 
                t.type IN (60,31) AND 
                DATE(t.created_time) BETWEEN :start and :end
            GROUP BY 
                WEEK(t.created_time, 6)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function refundsPerformedByCSPerWeek($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ WEEK(t.created_time, 6) AS 'Week #',
                SUM(t.amount) AS 'refunds amount (by CS)'
            FROM 
                `transactions` t
            WHERE
                DATE(t.created_time) BETWEEN :start and :end AND
                t.finalized = 1 AND
                (t.type NOT IN (11, 13, 16, 19, 20, 21, 22, 24, 27, 28 ,31, 32, 38, 40, 41, 42, 55, 56, 69, 114, 10, 109, 110, 111, 112, 113, 115, 116) OR (t.type = 82 AND t.staff_id IS NOT NULL))
            GROUP BY
                WEEK(t.created_time, 6)";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function callListCanceledSubscriptionsBetween2dates($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        // Find subscription_ids
        $cancelations = Yii::app()->rodb->createCommand("
        select c.cancelation_id, c.subscription_id
        from cancelation c
        inner join subscriptions s_customer on s_customer.subscription_id = c.subscription_id 
        where date(c.created) between :start and :end 
        ")
        ->bindValue(':start', $params['start'])
        ->bindValue(':end', $params['end'])
        ->queryAll();

        $subscriptions = array_column($cancelations, 'subscription_id');
        //What we get right from customer?
        $cfal = Yii::app()->rodb->createCommand("
        select 
            MAX(action_id) as cfal
        from cancelation_flow_action_logs 
        where 
            subscription_id in (" . implode(",", $subscriptions) . ") 
            and slide = 5 
            and text_input is not null
        group by subscription_id
        ")->queryAll();

        $cfal = array_column($cfal, 'cfal');
        // Client's Comment
        $cfal2 = Yii::app()->rodb->createCommand("
        select 
            MAX(action_id) as cfal2
        from cancelation_flow_action_logs 
        where 
            subscription_id in (" . implode(",", $subscriptions) . ") 
            and slide != 5 
            and text_input is not null
        group by subscription_id
        ")->queryAll();

        $cfal2 = array_column($cfal2, 'cfal2');
        //MAIN Query
        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ subscriptions.user_id AS 'User ID', 
                crg.name as 'Community',
                DATE(cancelation.created) AS 'Cancelation Date', 
                IF(subscriptions.user_id = cancelation.staff_id, 'Self cancelation', 'Cancelation by CS') AS 'Who canceled subscription', 
                DATE(last_order_date) AS 'Last order date',
                nb_orders_shipped AS 'Number of orders shipped since :date', 
                ROUND(nb_orders_shipped/nb_orders_generated*100) AS 'Take rate since :date',
                cancelation_reasons.reason AS 'Cancelation reason',
                DATE(t.last_communication_with_cs_before_cancelation) AS 'Last time client spoke to CS',
                cancelation.note AS 'Cancelation Note',
                cfal.text_input AS 'What did we get right?',
                cfal2.text_input AS 'Client\'s Comment',
                IF(nb_orders_shipped>0 , ROUND(abp_total_amount/ nb_orders_shipped, 2), 0) AS 'ABP'

            FROM
                cancelation
            INNER JOIN subscriptions ON (cancelation.subscription_id = subscriptions.subscription_id)
            INNER JOIN users ON (subscriptions.user_id = users.user_id AND users.never_call = 0)
            INNER JOIN community_representative_group crg on crg.community_representative_group_id = users.community_representative_group_id
            LEFT JOIN cancelation_reasons ON (cancelation_reasons.id = cancelation.reason)
            INNER JOIN (
                SELECT 
                    user_id, 
                    MIN(IF(status = 4, delivery_date, CURDATE())) AS first_order_date,
                    MAX(IF(status = 4, delivery_date, '0000-00-00')) AS last_order_date,
                    SUM(total_order_amount) AS total_amount_spent,
                    SUM(total_order_amount - total_national_tax - total_provincial_tax + discount - delivery_service_amount - donation_amount - total_consigne_amount) AS abp_total_amount,
                    SUM(IF(status = 4, 1, 0)) AS nb_orders_shipped,
                    COUNT(*) AS nb_orders_generated
                FROM 
                    orders 
                WHERE 
                    user_id IN (
                        SELECT 
                            user_id 
                        FROM 
                            cancelation 
                        INNER JOIN subscriptions ON (subscriptions.subscription_id = cancelation.subscription_id)
                        WHERE 
                            cancelation.created BETWEEN :start AND :end
                    ) AND 
                    delivery_date >= SUBDATE(:start, INTERVAL 6 MONTH)
                GROUP BY
                    user_id 
            ) o ON subscriptions.user_id = o.user_id
            LEFT JOIN (
                SELECT 
                    requestor_id,
                    MAX(IF(updated_at<cancelation.created, updated_at, NULL)) AS last_communication_with_cs_before_cancelation
                FROM 
                    tickets
                INNER JOIN subscriptions ON (subscriptions.user_id = tickets.requestor_id)
                INNER JOIN cancelation ON (cancelation.subscription_id = subscriptions.subscription_id AND cancelation.created BETWEEN :start AND :end)
                GROUP BY 
                    requestor_id
                ) t ON (t.requestor_id = subscriptions.user_id)
            LEFT JOIN cancelation_flow_action_logs cfal ON cfal.action_id IN (" . implode(",", $cfal) . ") AND cfal.subscription_id = cancelation.subscription_id
            LEFT JOIN cancelation_flow_action_logs cfal2 ON cfal2.action_id IN (" . implode(",", $cfal2) . ") AND cfal2.subscription_id = cancelation.subscription_id
            WHERE 
                cancelation.reason != 128 AND 
                subscriptions.active = 0 AND 
                cancelation.created BETWEEN :start AND :end AND 
                subscriptions.user_id NOT IN (SELECT user_id FROM `user_coupons` WHERE coupon_id IN (SELECT coupon_id FROM `coupons` WHERE used_for = :USED_FOR_TELESALES))
            GROUP BY 
                subscriptions.user_id";

        $output_row = Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->bindValue(':USED_FOR_TELESALES', Coupons::USED_FOR_TELESALES)->queryAll();

        $output_row = AppGlobal::recursive_change_key(
            $output_row, 
            [
                'Number of orders shipped since :date' => 'Number of orders shipped since '.date("Y-m-d", strtotime($params['start']." -6 months")),
                'Take rate since :date' => 'Take rate since '.date("Y-m-d", strtotime($params['start']." -6 months")),
            ]
        );
        return $output_row;
    }

    public static function retentionDueToLTV($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ t.agent_id AS 'Agent ID',
                blah.name AS 'Agent Name',
                t.requestor_id AS 'Client ID',
                t.ticket_id AS 'Ticket ID',
                tmp.body AS 'Comment callback'
            FROM
                tickets t 
            LEFT JOIN ticketActionsTaken ta ON (t.ticket_id = ta.ticket_id)
            LEFT JOIN ( 
                SELECT 
                    u.user_id,
                    u.name
                FROM
                    user_comms u
                WHERE 
                    u.in_use = 1)
            blah ON blah.user_id = t.agent_id
            LEFT JOIN (
                SELECT 
                        si.support_issue_id,
                        si.user_id, 
                        si.created_at,
                        si.created_by,
                        sic.body
                FROM 
                        support_issues si 
                LEFT JOIN support_issue_comments sic ON sic.support_issue_id = si.support_issue_id
            ) tmp on (tmp.user_id = t.requestor_id AND tmp.created_at BETWEEN t.assigned_to_operator_at AND DATE_ADD(t.assigned_to_operator_at, INTERVAL 24 HOUR))
            WHERE
                    t.status = 2 AND 
                    ta.ticket_action_id IN (6,9,15) AND 
                    t.ticket_reason_id = 11 AND        
                    DATE(t.created_at) BETWEEN :start AND :end AND 
                    tmp.body NOT IN ('NULL')
            GROUP BY 
                t.ticket_id";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function roughNumberOfSupersAtSignUpBetween2Dates($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ COUNT(s.user_id) AS 'rough number of supers'
            FROM 
                subscriptions s
            INNER JOIN users u ON u.user_id = s.user_id 
            WHERE 
                DATE(u.created) BETWEEN :start AND :end AND 
                s.type = 0 AND 
                s.active = 1";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function commsOptIn($params)
    {
        $sql =
            'SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ SUM(sms_open_marketplace_notifications),
                SUM(sms_basket_notifications),
                SUM(sms_customization_reminder),
                SUM(sms_activation_reminder),
                COUNT(*)
            FROM (
                SELECT
                    user_comms.user_id, 
                    user_comms.sms_open_marketplace_notifications, 
                    user_comms.sms_basket_notifications, 
                    user_comms.sms_customization_reminder, 
                    user_comms.sms_activation_reminder
                FROM
                    user_comms
                INNER JOIN subscriptions ON (subscriptions.user_id = user_comms.user_id AND subscriptions.active = 1)
                WHERE
                    user_comms.in_use = 1 AND 
                    user_comms.main = 1
            ) tmp';

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function fullPUPReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ DISTINCT(di.drop_instance_id) AS 'Drop Instance ID',
                d.name AS 'Droppoint Name',
                LEFT(d.zip_code,3) AS 'Zipcode prefixe',
                di.delivery_day AS 'Delivery day',
                COUNT(o.order_id) AS '# orders',
                di.capacity AS 'Drop Instance capacity',
                IFNULL(di.take_rate, 'N/A') AS 'Superlufavores Take Rate',
                IFNULL(di.take_rate_capacity, di.capacity) as 'Weekly Superlufavores Capacity',
                ROUND((COUNT(o.order_id)/di.capacity),4) AS 'Use rate'
            FROM 
                `droppoints` d 
            INNER JOIN drop_instance di ON (di.droppoint_id = d.droppoint_id)
            INNER JOIN orders o ON o.drop_instance_id = di.drop_instance_id AND o.total_order_amount > 0
            WHERE di.capacity>0 AND NOT (o.delivery_date < :start OR o.delivery_date > :end)
            GROUP BY di.drop_instance_id
        ";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function referralStats($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT status, count(user_id) FROM `subscription_snapshot` WHERE coupon like '%@%' and DATE(created) between :start and :end group by status";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function couponCostsPct($params) {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql = "SELECT o.coupon_id AS 'Coupon ID', c.name AS 'Name', c.code AS 'Code', 
                CASE 
                    WHEN c.used_for = 0 THEN 'Referral'
                    WHEN c.used_for = 1 THEN 'Event'
                    WHEN c.used_for = 2 THEN 'PUP'
                    WHEN c.used_for = 3 THEN 'Partner'
                    WHEN c.used_for = 4 THEN 'Corporate'
                    WHEN c.used_for = 5 THEN 'Others'
                    WHEN c.used_for = 6 THEN 'Gift credits'
                    WHEN c.used_for = 7 THEN 'Telesales'
                    WHEN c.used_for = 8 THEN 'Sales rep'
                    WHEN c.used_for = 9 THEN 'Digital'
                    ELSE 'Unknown' END AS 'Used for',
                COUNT(o.order_id) AS 'Nb orders',
                SUM(o.discount) AS 'Discount given'
                FROM orders o
                JOIN coupons c ON c.coupon_id = o.coupon_id
                WHERE o.status = 4
                AND o.delivery_date BETWEEN :start AND :end
                GROUP BY o.coupon_id
                HAVING SUM(o.discount) > 0;";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }
 
    public static function couponCosts($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ t.created_time, u.user_id, u.first_name, u.last_name, u.user_email, t.amount, us.user_email,  
                CASE 
                    WHEN t.type = 40 then 'coupon-added credits'
                    WHEN t.type = 41 then 'added credit-events and sponsorship'
                    WHEN t.type = 42 then 'Added credit-promotions'
                    ELSE 'unknown'
                END 'Transaction type', t.description
            FROM `transactions` t
            left join users u  on (u.user_id = t.user_id)
            left join users us  on (us.user_id = t.staff_id)
            WHERE type in (40, 42, 41) and DATE(t.created_time) BETWEEN :start AND :end
            ORDER BY `t`.`description` ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function campaignReferralStats($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT /*+ MAX_EXECUTION_TIME(900000) */ CASE
                WHEN nb_orders = 0 THEN 'ACTIVATIONS'
                ELSE 'REACTIVATION'
                END AS label,
                campaign,
                COUNT(*)
                FROM (
                select
                al.`user_id`,
                al.date,
                (SELECT COUNT(*) FROM securelufacom.orders WHERE orders.user_id = al.user_id AND orders.delivery_date < al.date) AS nb_orders,
                u.campaign_referral as campaign
                from securelufacom.`action_log` al
                join securelufacom.users u using (user_id)
                WHERE al.`date` BETWEEN :start AND :end AND al.`action_type` = 31
                ) t GROUP BY label, campaign
            ";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function naaTracking($params)
    {
        if (empty($params['end'])) {
            throw new Exception('Missing date params');
        }

        $sql =
            'SELECT
            /*+ MAX_EXECUTION_TIME(900000) */ uc.user_id,
            uc.name,
            uc.phone,
            uc.email,
            u.created
            FROM
            users u
            INNER JOIN user_comms uc ON
            u.user_id = uc.user_id AND uc.in_use = 1
            INNER JOIN subscriptions s ON
            u.user_id = s.user_id AND s.created < SUBDATE(CURDATE(), INTERVAL 4 WEEK) AND s.created > :end
            WHERE NOT
            EXISTS(
            SELECT
            *
            FROM
            orders o
            WHERE
            o.user_id = u.user_id AND o.status = 4
            ) AND u.never_call = 0            
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':end', $params['end'])->queryAll();
    }

    public static function referralsPerUser($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ user_id, 
                count(invited_user) 
            FROM 
                securelufacom.`referral_program_logs` 
            WHERE 
                DATE(created_at) BETWEEN :start and :end 
            GROUP BY 
                user_id 
            ORDER BY 
                count(invited_user) desc';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function signUpFlowABRevenueTracking($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ signup_variation,
                COUNT(DISTINCT(u.user_id)),
                ROUND(SUM(o.total_order_amount)) AS LTV,
                COUNT(o.order_id) AS nb_generated,
                SUM(IF(o.status = 4, 1, 0)) AS nb_shipped,
                ROUND((SUM(IF(o.status = 4, 1, 0)))/(COUNT(o.order_id))*100,2) as take_rate,
                ROUND((SUM(o.total_order_amount))/(SUM(IF(o.status = 4, 1, 0))),2) as ABP
            FROM users u 
            INNER JOIN subscriptions s ON u.user_id = s.user_id
            INNER JOIN orders o ON o.user_id = u.user_id 
            WHERE 
                s.start_date BETWEEN :start AND :end 
            GROUP BY 
                signup_variation
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function signUpFlowABPercentUsersReachingStep3($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ ROUND((SUM(CASE WHEN s.type = 0 AND s.state=2 AND signup_variation = 0 THEN 1 ELSE 0 END)/COUNT(*)*100), 2) AS super_avg
            FROM users u 
            INNER JOIN subscriptions s ON u.user_id = s.user_id
            WHERE 
                s.start_date BETWEEN :start AND :end
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function signUpFlowABPercentOfSupers($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ ROUND((SUM(CASE WHEN s.type = 0 AND s.state=4 AND signup_variation = 0 THEN 1 ELSE 0 END)/COUNT(*)*100), 2) AS super_avg
            FROM users u
            INNER JOIN subscriptions s ON u.user_id = s.user_id
            WHERE
                s.start_date BETWEEN :start AND :end
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function signUpFlowABCountSupers($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            'SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ ROUND(SUM(CASE WHEN s.type = 0 AND s.state=4 AND signup_variation = 0 THEN 1 ELSE 0 END))
            FROM users u 
            INNER JOIN subscriptions s ON u.user_id = s.user_id
            WHERE 
                s.start_date BETWEEN :start AND :end
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function workloadEstimateReportFor15Degrees($params)
    {
        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ sfo.delivery_date AS 'Expected Delivery Date',
                CASE
                    WHEN sfo.estimate_delivery_time = 0 THEN '12AM to 6AM'
                    WHEN sfo.estimate_delivery_time = 1 THEN '8AM to 6PM'
                    WHEN sfo.estimate_delivery_time = 2 THEN '6PM to 12AM'
                    ELSE 'UNKNOWN - WEIRD'
                END AS 'Time window',
                sfo.supplier_forecast_orders_id AS 'SFID',
                sfo.product_id AS 'Product ID',
                p.name AS 'Product Name (fr)',
                pl.l_name AS 'Product Name (en)',
                CASE
                    WHEN p.conservation_mode_in_warehouse = 0 THEN 'AMBIENT'
                    WHEN p.conservation_mode_in_warehouse = 1 THEN 'REFRIGERATED_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 2 THEN 'REFRIGERATED_15_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 3 THEN 'FROZEN'
                    WHEN p.conservation_mode_in_warehouse = 4 THEN 'OFFSITE_REFRIGERATED_4_DEGREES'
                    WHEN p.conservation_mode_in_warehouse = 5 THEN 'OFFSITE_FROZEN'
                    WHEN p.conservation_mode_in_warehouse = 6 THEN 'REFRIGERATED_8_DEGREES'
                    ELSE 'UNKNOWN - WEIRD'
                END AS 'Conservation mode in warehouse',
                p.volume AS 'Volume (cm3)',
                (sfo.number_of_units_ordered * IF(sfo.reception_type = 4, floor(sfo.reception_weight/p.weight), 1)) AS 'Inventory (units)',
                ((sfo.number_of_units_ordered * IF(sfo.reception_type = 4, floor(sfo.reception_weight/p.weight), 1)) * p.volume) AS 'Total Volume (cc)',
                p.storage_format_in_wh AS 'Warehouse Storage Format',
                tf.internal_name AS 'Tote Format',
                '-' AS 'Number of totes required',
                '-' AS 'Number of towers'
            FROM 
                supplier_forecast_orders sfo
            INNER JOIN products p ON (p.product_id = sfo.product_id)
            LEFT JOIN productsLang pl ON (pl.product_id = sfo.product_id AND pl.lang_id = 'en')
            LEFT JOIN tote_formats tf ON (tf.tote_format_id = p.tote_format_id)
            WHERE 
                sfo.status = 3 AND 
                p.conservation_mode_in_warehouse = 2
            ORDER BY 
                sfo.delivery_date,
                sfo.estimate_delivery_time
            ";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function inventoryTurnOver($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
            'SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ :start AS "Beg. period",
                :end AS "End. period",
                s.name AS "Supplier Name (FR)",
                sl.l_name AS "Supplier Name (EN)",
                p.product_id AS "Product ID",
                p.name AS "Product Name (FR)",
                pl.l_name AS "Product Name (EN)",
                psc.name AS "Sub-Category (FR)",
                pscl.l_name AS "Sub-Category (EN)",
                pssc.name AS "Sub-Sub-Category (FR)",
                psscl.l_name AS "Sub-Sub-Category (EN)",
                p.weight AS "Product Weight (g)",
                p.volume AS "Product Volume (cm3)",
                IF(p.flow_through = 1, "Yes", "No") AS "Is A Flow-through Product ?",
                CASE
                    WHEN p.conservation_mode_in_warehouse = 0 THEN "AMBIENT"
                    WHEN p.conservation_mode_in_warehouse = 1 THEN "REFRIGERATED_4_DEGREES"
                    WHEN p.conservation_mode_in_warehouse = 2 THEN "REFRIGERATED_15_DEGREES"
                    WHEN p.conservation_mode_in_warehouse = 3 THEN "FROZEN"
                    WHEN p.conservation_mode_in_warehouse = 4 THEN "OFFSITE_REFRIGERATED_4_DEGREES"
                    WHEN p.conservation_mode_in_warehouse = 5 THEN "OFFSITE_FROZEN"
                    WHEN p.conservation_mode_in_warehouse = 6 THEN "REFRIGERATED_8_DEGREES"
                    ELSE "UNKNOWN - WEIRD"
                END AS "WH Storage Temp",
                CONCAT(u.first_name, " ", u.last_name) AS "Purchaser",
                CONCAT(u1.first_name, " ", u1.last_name) AS "Category Manager",
                ROUND(IFNULL(qty_sold_stats.qty_sold, 0) / ((IFNULL(inv_beg_period.inv, 0) + IFNULL(inv_end_period.inv, 0))/2),2) AS "Inventory Turnover Ratio",
                ROUND(365 / (ROUND(IFNULL(qty_sold_stats.qty_sold, 0) / ((IFNULL(inv_beg_period.inv, 0) + IFNULL(inv_end_period.inv, 0))/2),2)), 2) AS "Inventory Turnover Time Period",
                IFNULL(qty_sold_stats.distinct_days_where_product_was_sold,0) AS "Number of distinct days where product was sold",
                IFNULL(actual_inventory.qty_on_hand, 0) AS "# Units In Inventory Currently",
                (IFNULL(actual_inventory.qty_on_hand, 0) * p.volume) AS "Total Volume Used Currently (cm3)",
                IFNULL(qty_sold_stats.qty_sold, 0) AS "Qty Sold During Period (DEBUG)",
                IFNULL(inv_beg_period.inv, 0) AS "Qty At Beginning Of Period (DEBUG)",
                IFNULL(inv_end_period.inv, 0) AS "Qty At End Of Period (DEBUG)"
            FROM
                products p
            LEFT JOIN suppliers s ON (s.supplier_id = p.supplier_id)
            LEFT JOIN suppliersLang sl ON (sl.supplier_id = p.supplier_id AND sl.lang_id = "en")
            LEFT JOIN productsLang pl ON (pl.product_id = p.product_id AND pl.lang_id = "en")
            LEFT JOIN productSubSubCategories pssc ON (pssc.sub_sub_id = p.sub_sub_id)
            LEFT JOIN productSubSubCategoriesLang psscl ON (psscl.sub_sub_id = pssc.sub_sub_id AND psscl.lang_id = "en")
            LEFT JOIN productSubCategories psc ON (psc.subcategory_id = pssc.subcategory_id)
            LEFT JOIN productSubCategoriesLang pscl ON (pscl.subcategory_id = psc.subcategory_id AND pscl.lang_id = "en")
            LEFT JOIN users u ON (u.user_id = p.purchaser_id)
            LEFT JOIN users u1 ON (u1.user_id = p.category_manager_id)
            LEFT JOIN (
                SELECT
                    ps.product_id,
                    SUM(ps.qty_sold) AS qty_sold,
                    SUM(IF(ps.qty_sold>0, 1, 0)) AS distinct_days_where_product_was_sold
                FROM
                    product_stats ps
                WHERE
                    ps.date BETWEEN :start AND :end
                GROUP BY
                    ps.product_id
            ) qty_sold_stats ON (qty_sold_stats.product_id = p.product_id)
            LEFT JOIN (
                SELECT
                    product_id,
                    SUM(quantity) as inv
                FROM
                    inventory_snapshots
                WHERE
                    DATE(created) = :start
                GROUP BY
                    product_id
            ) inv_beg_period ON (inv_beg_period.product_id = p.product_id)
            LEFT JOIN (
                SELECT
                    product_id,
                    SUM(quantity) as inv
                FROM
                    inventory_snapshots
                WHERE
                    DATE(created) = :end
                GROUP BY
                    product_id
            ) inv_end_period ON (inv_end_period.product_id = p.product_id)
            LEFT JOIN (
                SELECT
                    product_id,
                    SUM(number_of_units_available) AS qty_on_hand
                FROM
                    inventory
                WHERE
                    number_of_units_available>0
                GROUP BY
                    product_id
            ) actual_inventory ON (actual_inventory.product_id = p.product_id)
            WHERE
                ( IFNULL(qty_sold_stats.qty_sold, 0) > 0 OR IFNULL(inv_beg_period.inv, 0) > 0 OR IFNULL(inv_end_period.inv, 0) > 0 )
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValues([':start' => $params['start'], ':end' => $params['end']])->queryAll();
    }

    public static function hoursPerRole($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ IFNULL(di.department_name, '-') as 'Parent Department',
                IFNULL(d.department_name, '-') as 'Department',
                IFNULL(r.name, 'NOT SET') as 'Role',
                IFNULL(ds.name, '-') as 'Shift',
                IFNULL(ROUND(SUM(IFNULL(pt.punch_time, wtt.work_time)) / 60 / 60), 0) as 'Hours of Work'
            FROM
                users u 
            LEFT JOIN (
                SELECT
                    SUM(TIME_TO_SEC(TIMEDIFF(IF(status = :task_done,finished_at, NOW()),started_at))) - IF(task_type_defaults_id = :generaldc AND TIMESTAMPDIFF(SECOND,started_at, finished_at) >= 10 * 60 * 60, 30 * 60, 0) as work_time,
                    assigned_to
                FROM 
                    task
                WHERE 
                    status IN (:task_done, :in_progress)
                    AND date(IF(status = :task_done,finished_at,started_at)) BETWEEN :start AND :end
                GROUP BY 
                    assigned_to
                ) as wtt ON wtt.assigned_to = u.user_id
            LEFT JOIN (
                SELECT
                    employee_id,
                    SUM(IF(stat_holiday_id IS NULL,minutes * 60,0)) as time_added,
                    SUM(IF(stat_holiday_id IS NOT NULL,minutes * 60,0)) as stat_time
                FROM 
                    manual_hours
                WHERE 
                    date BETWEEN :start AND :end
                GROUP BY 
                    employee_id
            ) as mh ON mh.employee_id = u.user_id
            LEFT JOIN (
                SELECT
                    SUM(TIME_TO_SEC(TIMEDIFF(IFNULL(stop,NOW()),start))) as punch_time,
                    user_id
                FROM 
                    payment_logging
                WHERE 
                    date(start) BETWEEN :start AND :end
                GROUP BY 
                    user_id
            ) as pt ON pt.user_id = u.user_id
            LEFT JOIN
                user_roles ur ON ur.user_id = u.user_id
            LEFT JOIN
                roles r ON r.role_id = ur.role_id
            LEFT JOIN
                department_shifts ds ON ds.department_shift_id = ur.default_shift_id
            LEFT JOIN
                temp_departments d ON d.department_id = r.department_id
            LEFT JOIN
                temp_departments di ON di.department_id = d.parent_id
            WHERE 
                u.employee_status != 'Non employee'
            GROUP BY
                ur.role_id, ur.default_shift_id
            ORDER BY 
                di.department_name ASC,
                d.department_name ASC,
                r.name ASC
            ";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':task_done' => Task::STATUS_DONE,
            ':in_progress' => Task::STATUS_IN_PROGRESS,
            ':generaldc' => TaskTypeDefaults::GeneralDC,
        ])->queryAll();
    }

    public static function usersRole($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ IFNULL(u.internal_employee_id, '-') as 'EmployeurD #',
                u.user_id as 'User #',
                CONCAT(u.first_name, ' ', u.last_name) as name, 
                u.user_email as 'User email',
                IF(u.status = 0, 'Inactive', 'Active') as 'Employee Status',
                IFNULL(diL.l_department_name, '-') as 'Parent Department',
                IFNULL(dL.l_department_name, '-') as 'Department',
                IFNULL(rL.l_name, 'NOT SET') as 'Role',
                IFNULL(ds.name, '-') as 'Shift'
            FROM
                users u 
            LEFT JOIN
                user_roles ur ON ur.user_id = u.user_id
            LEFT JOIN
                roles r ON r.role_id = ur.role_id
            LEFT JOIN
                roles_lang rL on rL.role_id = r.role_id AND rL.lang_id = 'en'
            LEFT JOIN
                department_shifts ds ON ds.department_shift_id = ur.default_shift_id
            LEFT JOIN
                temp_departments d ON d.department_id = r.department_id
            LEFT JOIN
                temp_departments_lang dL ON dL.department_id = d.department_id AND dL.lang_id = 'en'
            LEFT JOIN
                temp_departments di ON di.department_id = d.parent_id
            LEFT JOIN 
                temp_departments_lang diL on diL.department_id = di.department_id AND diL.lang_id = 'en'
            WHERE 
                u.employee_status != 'Non employee' AND (u.bonus_program = 1 OR u.user_id IN (SELECT DISTINCT user_id FROM payment_logging))
            ORDER BY
                u.internal_employee_id ASC
            ";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function dailyRPCSupplierForecastedNeed($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ date,
                CONCAT('#', s.supplier_id, ' - ', IFNULL(sl.l_name, s.name)) as supplier,
                CONCAT(t.internal_name, ' (', t.height, 'in)') as tote_format, 
                IF(r.rpc_in_accepted = 0, rpc_in_ordered, rpc_in_accepted) as 'RPC In',
                r.rpc_out as 'RPC out'
            FROM
                suppliers_rpc r
            INNER JOIN
                suppliers s ON s.supplier_id = r.supplier_id
            LEFT JOIN
                suppliersLang sl ON sl.supplier_id = s.supplier_id AND lang_id = :lang
            LEFT JOIN
                tote_formats t ON t.tote_format_id = r.tote_format_id
            WHERE
                date BETWEEN :start AND :end
            ORDER BY date ASC, r.supplier_id ASC, r.tote_format_id ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':lang' => Yii::app()->language,
        ])->queryAll();
    }

    public static function driverBreaks($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ b.route_id AS 'ROUTE',
                date(b.start_time) as 'DELIVERY DATE',
                CONCAT(u.first_name, ' ', u.last_name) as 'DRIVER',
                c.name as 'COMPANY',
                wwv.external_id as 'VEHICLE ID',                
                DATE_FORMAT(b.start_time,'%H:%i') as 'START TIME',
                DATE_FORMAT(b.end_time,'%H:%i') as 'END TIME',
                b.photo_url as 'PHOTO',
                IFNULL(prm.message, '-') AS 'PHOTO REJECTION',
                IF(v.violation_id IS NOT NULL, 'Yes', 'No') AS 'BREAK TIME VIOLATION'
            FROM
                driver_break b
            INNER JOIN
                ww_routes wwr ON wwr.route_id = b.route_id
            INNER JOIN 
                ww_vehicles wwv ON wwv.vehicle_id = wwr.vehicle_id
            LEFT JOIN
                violations v ON v.ww_step_id = b.step_id AND v.violation_type_id = :type_break_exceeds_time
            LEFT JOIN
                photo_reject_messages prm ON (prm.step_id = b.step_id AND prm.photo_type = :type_break) 
            LEFT JOIN
                users u ON u.user_id = b.user_id
            LEFT JOIN
                companies c ON c.company_id = u.company_id
            WHERE
                date(b.start_time) BETWEEN :start AND :end
            ";

        $data = Yii::app()->rodb->createCommand($sql)->bindValues([
                ':start' => $params['start'],
                ':end' => $params['end'],
                ':type_break' => PhotoRejectMessages::PHOTO_TYPE_BREAK,
                ':type_break_exceeds_time' => Violations::VIOLATION_BREAK_EXCEEDS_TIME_LIMIT,
            ])->queryAll();

        foreach ($data as $key => &$value) {
            $value['PHOTO'] = '("'.Yii::app()->s3images->getImage('deliveries', $value['PHOTO']).'")';
        }

        return $data;
    }

    public static function emailMetrics($params)
    {
        $sql =
            "SELECT
            /*+ MAX_EXECUTION_TIME(900000) */ ROUND((SUM(IF(uc.email_customization_reminder = 1, 1, 0))/blah.c1)*100, 2) AS 'Pct. Email - Customization',
            ROUND((SUM(IF(uc.email_activation_reminder = 1, 1, 0))/blah.c1)*100, 2) AS 'Pct. Email - Activation',
            ROUND((SUM(IF(uc.sms_open_marketplace_notifications = 1, 1, 0))/blah.c1)*100, 2) AS 'Pct. SMS - MP Open',
            ROUND((SUM(IF(uc.sms_customization_reminder = 1, 1, 0))/blah.c1)*100, 2) AS 'Pct. SMS - Customization',
            ROUND((SUM(IF(uc.sms_basket_notifications = 1, 1, 0))/blah.c1)*100, 2) AS 'Pct. SMS - Basket Notifications',
            ROUND((SUM(IF(uc.signed_up_to_newsletter = 1, 1, 0))/blah.c1)*100, 2) AS 'Pct. Subbed to NL',
            SUM(IF(uc.signed_up_to_newsletter = 1 AND s.type = 0, 1, 0)) AS 'Super Lufavores Subbed to NL',
            SUM(IF(uc.signed_up_to_newsletter = 1 AND s.type = 1, 1, 0)) AS 'Lufavores Subbed to NL',
            SUM(IF(uc.signed_up_to_newsletter = 1, 1, 0)) AS 'Total Subbed to NL'
        FROM user_comms uc 
        INNER JOIN subscriptions s ON
            uc.user_id = s.user_id AND s.active = 1
        INNER JOIN (SELECT COUNT(*) AS 'c1' FROM user_comms uc INNER JOIN subscriptions s ON uc.user_id = s.user_id AND s.active = 1 WHERE uc.main = 1 AND uc.in_use = 1) AS blah
        WHERE
            uc.main = 1 AND uc.in_use = 1         
            ";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function lufavoreSwitch($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
            "SELECT
            /*+ MAX_EXECUTION_TIME(900000) */ COUNT(*) AS 'No. Switched to Lufavore'
        FROM
            securelufacom.action_log al
        INNER JOIN securelufacom.subscriptions ss ON
            al.user_id = ss.user_id
        WHERE
            al.action_type = :action_type AND al.date BETWEEN :start AND :end     
            ";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':action_type' => ActionLog::TYPE_SUBSCRIPTION_SWITCHED_TO_REQUIRE_ACTIVATION,
        ])->queryAll();
    }

    public static function outerRegionPerformance($params)
    {
        if (empty($params['start']) || empty($params['end']) || empty($params['default_group_by'])) {
            throw new Exception('Missing date range values');
        }

        $group_by = self::getGroupByForQuery('default_group_by', $params['default_group_by'], 'o.delivered');
        if ($group_by == '') {
            $group_by = 'o.delivered';
        }

        $dateSelectClause = "o.delivered as 'Delivered on'";
        if ($group_by == "DATE_FORMAT(o.delivered, '%Y-%m')") {
            $dateSelectClause = 'MONTHNAME(o.delivered) as Month';
        }
        if ($group_by == "DATE_FORMAT(o.delivered, '%Y-%V')") {
            $dateSelectClause = "DATE_FORMAT(o.delivered, '%Y-%V') AS 'Week'";
        }

        $sql = "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ o.region AS Region,
                {$dateSelectClause},
                SUM(IF(o.delivery_method = 'PUP',1,0)) AS PUP,
                SUM(IF(o.delivery_method = 'HD',1,0)) AS HD,
                SUM(o.order_id) AS 'Total Orders',
                ROUND(SUM(o.order_amount),2) AS 'Total Value',
                ROUND((SUM(o.order_amount) / COUNT(o.order_id)), 2) AS ABP,
                o.order_status AS 'Order Status'
            FROM
                (SELECT
                    IF(o.order_id IS NOT NULL,1,0) as order_id,
                    CASE
                        WHEN o.status = :STATUS_PROCESSED THEN 'Shipped'
                        WHEN o.status = :STATUS_CANCELED THEN 'Cancelled'
                        WHEN o.status = :STATUS_NON_ACTIVATED THEN 'Not Activated'
                        ELSE 'Other' 
                        END as order_status,
                    DATE(o.delivery_date) as delivered,
                    o.total_order_amount as order_amount,
                    CASE 
                        WHEN dp.hd_product_id IS NULL THEN 'PUP'
                        WHEN dp.hd_product_id IS NOT NULL THEN 'HD'
                        ELSE 0
                    END AS delivery_method,
                    IF(dp.hd_product_id IS NULL, dp.zip_code, uhd.zip_code) as zip_code,
                    z.zipcode_prefix,
                    z.region
                FROM (SELECT * FROM orders WHERE status = 4 AND DATE(delivery_date) BETWEEN :start AND :end) o
                LEFT JOIN user_home_deliveries uhd ON uhd.order_id = o.order_id
                LEFT JOIN droppoints dp ON (dp.droppoint_id = o.droppoint_id)
                LEFT JOIN (SELECT region, LEFT(zipcode, 3) as zipcode_prefix FROM zipcodes GROUP BY LEFT(zipcode,3)) z ON (z.zipcode_prefix = LEFT(IF(dp.hd_product_id IS NULL, dp.zip_code, uhd.zip_code),3))
                ) o
            GROUP BY {$group_by}, o.region, o.order_status
            ORDER BY o.region ASC, 'Date' DESC, o.order_status ASC";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':STATUS_PROCESSED' => Orders::STATUS_PROCESSED,
            ':STATUS_CANCELED' => Orders::STATUS_CANCELED,
            ':STATUS_NON_ACTIVATED' => Orders::STATUS_NON_ACTIVATED,
        ])->queryAll();
    }

    public static function communityEventsExpenseReport($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql = 'SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ s.scheduled_events_id as "Scheduled events id",
                IF(:lang = "en_us", s_l.l_name, s.name) as "Event name",
                CASE 
                    WHEN c.category = :TYPE_PUBLIC THEN "Public"
                    WHEN c.category = :TYPE_PRIVATE THEN "Private"
                    END as category,
                c.community_events_id as "Community events id",
                IF(:lang = "en_us", c_l.l_name, c.name) as "Event type",
                ROUND(IF(attendees.profit IS NOT NULL, attendees.profit + attendees.TPS_preceived + attendees.TVQ_perceived, 0),2) as "Total amount perceived",
                ROUND(IF(attendees.profit IS NOT NULL, attendees.TPS_preceived, 0),2) as "Total TPS perceived",
                ROUND(IF(attendees.profit IS NOT NULL, attendees.TVQ_perceived, 0),2) as "Total TVQ perceived"
            FROM scheduled_events s
            LEFT JOIN community_events c ON c.community_events_id = s.community_events_id
            LEFT JOIN scheduled_events_lang s_l ON s_l.scheduled_events_id = s.scheduled_events_id
            LEFT JOIN community_events_lang c_l ON c_l.community_events_id = c.community_events_id
            LEFT JOIN (
                SELECT
                    g.scheduled_events_id,
                    SUM(g.quantity) as quantity,
                    SUM(g.price_paid * g.quantity) as profit,
                    SUM(g.price_paid * g.quantity * :NATIONAL_TAX_VALUE) as TPS_preceived,
                    SUM(g.price_paid * g.quantity * :PROVINCIAL_TAX_VALUE) as TVQ_perceived
                FROM community_events_guests g
                WHERE 
                    g.status != :guest_canceled
                GROUP BY g.scheduled_events_id
            ) attendees ON attendees.scheduled_events_id = s.scheduled_events_id
            WHERE s.start_date BETWEEN :start AND :end
            AND c.default_price > 0
            AND s.status IN (:published, :past)';

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':guest_canceled' => CommunityEventsGuests::STATUS_CANCELED,
            ':published' => ScheduledEvents::STATUS_PUBLISHED,
            ':past' => ScheduledEvents::STATUS_PAST,
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':lang' => Yii::app()->language,
            ':TYPE_PUBLIC' => CommunityEvents::TYPE_PUBLIC,
            ':TYPE_PRIVATE' => CommunityEvents::TYPE_PRIVATE,
            ':NATIONAL_TAX_VALUE' => Orders::NATIONAL_TAX_VALUE / 100,
            ':PROVINCIAL_TAX_VALUE' => Orders::PROVINCIAL_TAX_VALUE / 100,
        ])->queryAll();
    }

    public static function locksByDay($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
        'SELECT 
	        /*+ MAX_EXECUTION_TIME(900000) */ DATE(slc.created_at) AS "Date",
	        slc.reason AS "Reason ID",
            slr.en_value AS "Reason",
	        COUNT(*) AS "No. Locks"
        FROM 
	        securelufacom.lock_comments slc
        INNER JOIN securelufacom.lock_reasons slr ON
	        slc.reason = slr.lock_type_id
        WHERE 
	        DATE(slc.created_at) BETWEEN :start AND :end
        GROUP BY 
	        DATE(slc.created_at), slc.reason
        ORDER BY 
	        DATE(slc.created_at), slc.reason';

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
        ])->queryAll();
    }

    public static function locksByEmployee($params)
    {
        if (empty($params['start']) || empty($params['end']) || empty($params['min_locks'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
            "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ slc.given_to AS 'User ID',
            su.internal_employee_id AS 'Employee ID',
            CONCAT(su.first_name, ' ', su.last_name) AS 'Name',
            COUNT(*) AS 'Total No. Locks',
            SUM(IF(slc.reason = 1, 1, 0)) AS 'Reason 1 Locks',
            SUM(IF(slc.reason = 2, 1, 0)) AS 'Reason 2 Locks',
            SUM(IF(slc.reason = 3, 1, 0)) AS 'Reason 3 Locks',
            SUM(IF(slc.reason = 4, 1, 0)) AS 'Reason 4 Locks',
            SUM(IF(slc.reason = 5, 1, 0)) AS 'Reason 5 Locks',
            SUM(IF(slc.reason = 6, 1, 0)) AS 'Reason 6 Locks',
            SUM(IF(slc.reason = 7, 1, 0)) AS 'Reason 7 Locks',
            SUM(IF(slc.reason = 8, 1, 0)) AS 'Reason 8 Locks',
            SUM(IF(slc.reason = 9, 1, 0)) AS 'Reason 9 Locks',
            SUM(IF(slc.reason = 10, 1, 0)) AS 'Reason 10 Locks'
        FROM
            securelufacom.lock_comments slc
                INNER JOIN
            securelufacom.users su ON slc.given_to = su.user_id
        WHERE
            DATE(slc.created_at) BETWEEN :start AND :end
        GROUP BY slc.given_to
        HAVING `Total No. Locks` >= :min_locks
        ORDER BY slc.given_to;";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':min_locks' => $params['min_locks'],
        ])->queryAll();
    }

    public static function ordersByTranche($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
        'SELECT
            /*+ MAX_EXECUTION_TIME(900000) */ o.delivery_date,
            SUM(IF(o.total_order_amount < 50, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount < 50, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 50 AND o.total_order_amount < 60, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 50 AND o.total_order_amount < 60, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 60 AND o.total_order_amount < 70, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 60 AND o.total_order_amount < 70, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 70 AND o.total_order_amount < 80, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 70 AND o.total_order_amount < 80, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 80 AND o.total_order_amount < 90, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 80 AND o.total_order_amount < 90, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 90 AND o.total_order_amount < 100, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 90 AND o.total_order_amount < 100, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 100 AND o.total_order_amount < 110, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 100 AND o.total_order_amount < 110, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 110 AND o.total_order_amount < 120, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 110 AND o.total_order_amount < 120, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 120 AND o.total_order_amount < 150, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 120 AND o.total_order_amount < 150, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 150 AND o.total_order_amount < 200, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 150 AND o.total_order_amount < 200, 1, 0)) / COUNT(*)) * 100), 2),
            SUM(IF(o.total_order_amount >= 200, 1, 0)),
            ROUND(((SUM(IF(o.total_order_amount >= 200, 1, 0)) / COUNT(*)) * 100), 2),
            COUNT(*)
        FROM
            orders o
        WHERE
            o.delivery_date BETWEEN :start AND :end AND o.status = 4
        GROUP BY o.delivery_date';

        $data = Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
        ])->queryAll(true);
        array_unshift($data, [
            'Delivery Date',
            '$50 ou moins',
            '%',
            '$50-$59.99',
            '%',
            '$60-$69.99',
            '%',
            '$70-$79.99',
            '%',
            '$80-$89.99',
            '%',
            '$90-$99.99',
            '%',
            '$100-$109.99',
            '%',
            '$110-$119.99',
            '%',
            '$120-$149.99',
            '%',
            '$150-$199.99',
            '%',
            '$200 ou plus',
            '%',
            'Commandes Totale',
        ]);

        return $data;
    }

    public static function salesByProductPerBasketType($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
        'SELECT
        /*+ MAX_EXECUTION_TIME(900000) */ p.product_id AS "ProductID",
        s.name AS "Supplier",
        p.name AS "Product Name",
        ROUND(od.purchase_price_per_unit, 2) AS "Purchase Price",
        PI.current_price AS "Sale Price",
        COUNT(od.order_details_id) AS "QTY Sold",
        ROUND((COUNT(od.order_details_id) * PI.current_price), 2) AS "Revenue ($)",
        (100-(ROUND(od.purchase_price_per_unit / PI.current_price, 2) * 100)) AS "Margin (%)",
        sq2.total_sales AS "Total MP Sales",
        ((ROUND((COUNT(od.order_details_id) * PI.current_price), 2)) / sq2.total_sales) * 100 AS "Pct Total Sales",
        SUM(IF(b.basket_id = 1, 1, 0)) AS "No. Small Baskets",
        SUM(IF(b.basket_id = 5, 1, 0)) AS "No. Type 5 Baskets",
        SUM(IF(b.basket_id = 7, 1, 0)) AS "No. Type 7 Baskets",
        SUM(IF(b.basket_id = 8, 1, 0)) AS "No. Sans Minimum Baskets",
        SUM(IF(b.basket_id = 9, 1, 0)) AS "No. Type 9 Baskets"
    FROM
        suppliers s
    INNER JOIN products p ON
        s.supplier_id = p.supplier_id
    INNER JOIN order_details od ON
        p.product_id = od.product_id
    INNER JOIN orders o ON
        od.order_id = o.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start AND :end
    INNER JOIN products_inventory PI ON
        od.product_id = PI.product_id AND o.delivery_date = PI.inventory_date
    LEFT JOIN basket_designs bi ON
        o.basket_design_id = bi.basket_design_id
    LEFT JOIN baskets b ON
        bi.basket_id = b.basket_id
    INNER JOIN (
        SELECT
            ROUND(SUM(o2.total_order_amount), 2) total_sales
        FROM orders o2
        WHERE o2.status = 4 AND o2.delivery_date BETWEEN :start AND :end) sq2
    GROUP BY
        p.product_id
    ORDER BY
        p.product_id;';

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
        ])->queryAll();
    }

    public static function salesByProductWithFeatured($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }
        $sql =
        'SELECT
        /*+ MAX_EXECUTION_TIME(900000) */ p.product_id AS "ProductID",
        s.name AS "Supplier",
        p.name AS "Product Name",
        ROUND(od.purchase_price_per_unit, 2) AS "Purchase Price",
        PI.current_price AS "Sale Price",
        sq1.days_featured AS "No. Days Featured",
        COUNT(od.order_details_id) AS "QTY Sold",
        ROUND((COUNT(od.order_details_id) * PI.current_price), 2) AS "Revenue ($)",
        (100-(ROUND(od.purchase_price_per_unit / PI.current_price, 2) * 100)) AS "Margin (%)",
        sq2.total_sales AS "Total MP Sales",
        ((ROUND((COUNT(od.order_details_id) * PI.current_price), 2)) / sq2.total_sales) * 100 AS "Pct Total Sales"
    FROM
        suppliers s
    INNER JOIN products p ON
        s.supplier_id = p.supplier_id
    INNER JOIN order_details od ON
        p.product_id = od.product_id
    INNER JOIN orders o ON
        od.order_id = o.order_id AND o.status = 4 AND o.delivery_date BETWEEN :start AND :end
    INNER JOIN products_inventory PI ON
        od.product_id = PI.product_id AND o.delivery_date = PI.inventory_date
    INNER JOIN (
        SELECT
            pi2.product_id product_id,
            pi2.inventory_date inventory_date,
            SUM(IF(pi2.is_featured = 1, 1, 0)) days_featured
        FROM
            products_inventory pi2
        WHERE pi2.inventory_date BETWEEN :start AND :end
        GROUP BY pi2.product_id
        ) sq1 ON 
            sq1.product_id = p.product_id
    INNER JOIN (
        SELECT
            ROUND(SUM(o2.total_order_amount), 2) total_sales
        FROM orders o2
        WHERE o2.status = 4 AND o2.delivery_date BETWEEN :start AND :end) sq2
    GROUP BY
        p.product_id
    ORDER BY
        p.product_id;';

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
        ])->queryAll();
    }

    public static function inventoryPerLot($params)
    {
        if (empty($params['start'])) {
            throw new Exception('Missing date params');
        }

        $sql =
            'SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ CONCAT(
                LEFT(i.lot_id, 3),
                " ",
                RIGHT(i.lot_id, 3)
            ) AS "Lot ID",
            lh.display_location AS "Location",
            p.product_id AS "PID",
            sl.l_name AS "Supplier Name (EN)",
            pl.l_name AS "Product Name (EN)",
            pscl.l_name AS "Sub-Category (EN)",
            psscl.l_name AS "Sub-Sub-Category (EN)",
            p.volume AS "Volume",
            p.weight AS "Weight",
            i.initial_quantity AS "Initial Qty",
            i.number_of_units_available AS "Current Qty",
            (
                i.number_of_units_available * p.price
            ) AS "Total Value",
            sfo.reception_timestamp AS "Reception Date",
            i.Expiry_date AS "Expiry"
        FROM
            inventory i
        LEFT JOIN location_header lh ON
            lh.location_header_id = i.location_header_id
        LEFT JOIN products p ON
            p.product_id = i.product_id
        LEFT JOIN suppliers s ON
            (s.supplier_id = p.supplier_id)
        LEFT JOIN suppliersLang sl ON
            (
                sl.supplier_id = p.supplier_id AND sl.lang_id = "en"
            )
        LEFT JOIN productsLang pl ON
            (
                pl.product_id = p.product_id AND pl.lang_id = "en"
            )
        LEFT JOIN productSubSubCategories pssc ON
            (pssc.sub_sub_id = p.sub_sub_id)
        LEFT JOIN productSubSubCategoriesLang psscl ON
            (
                psscl.sub_sub_id = pssc.sub_sub_id AND psscl.lang_id = "en"
            )
        LEFT JOIN productSubCategories psc ON
            (
                psc.subcategory_id = pssc.subcategory_id
            )
        LEFT JOIN productSubCategoriesLang pscl ON
            (
                pscl.subcategory_id = psc.subcategory_id AND pscl.lang_id = "en"
            )
        LEFT JOIN products_inventory PI ON
            (PI.product_id = p.product_id) AND PI.inventory_date = :start
        LEFT JOIN supplier_forecast_orders sfo ON
            (
                sfo.supplier_forecast_orders_id = i.supplier_forecast_orders_id
            )
        WHERE
            i.number_of_units_available > 0 AND psc.subcategory_id != 382
        ORDER BY
            p.product_id,
            sfo.reception_timestamp ASC           
            ';

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->queryAll();
    }

    public static function storageLocationUsage($params)
    {
        $sql ="SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ lh.display_location AS 'Storage location', 
                lh.floor AS 'Floor', 
                lh.aisle AS 'Aisle', 
                lh.bay AS 'Bay', 
                IF(warehouse_path.warehouse_path_id IS NULL, 'No', 'Yes') AS 'Location in WH path ?',
                IFNULL(inventory.lot_id, '-') AS 'Lot id', 
                IFNULL(inventory.product_id, '-') AS 'Product id', 
                IFNULL(productsLang.l_name, '-') AS 'Product name', 
                IFNULL(inventory.number_of_units_available, '-') AS 'Units in lot',
                IF(inventory.isPortioned IS NULL, '-', IF(inventory.isPortioned = 1, 'Portioned', 'Not portioned')) AS 'Portioning status'
            FROM 
                `location_header` lh 
            LEFT JOIN inventory ON (inventory.location_header_id = lh.location_header_id AND inventory.number_of_units_available > 0) 
            LEFT JOIN productsLang ON (productsLang.product_id = inventory.product_id) 
            LEFT JOIN warehouse_path ON (warehouse_path.floor = lh.floor AND warehouse_path.aisle = lh.aisle AND warehouse_path.bay = lh.bay)
            WHERE 
                lh.floor = 'C' 
            ORDER BY `lh`.`display_location` ASC";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function liveInventoryForAudit($params)
    {
        $sql =
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ lh.display_location AS 'Storage location',
                i.lot_id AS 'Lot ID',
                i.product_id AS 'Product ID',
                p.name AS 'Product name',
                s.name AS 'Supplier',
                p.units AS 'Retail unit',
                p.weight AS 'Weight in grams/liters',
                i.number_of_units_available AS 'Qty in lot without picking activities',
                pa.qty_picked AS 'Quantity picked',
                (i.number_of_units_available - IFNULL(pa.qty_picked, 0)) AS 'Quantity actually in lot',
                NOW() AS 'Date of report'
            FROM
                inventory i
            LEFT JOIN location_header lh ON (lh.location_header_id = i.location_header_id)
            LEFT JOIN products p ON (p.product_id = i.product_id)
            LEFT JOIN suppliers s ON (p.supplier_id = s.supplier_id)
            LEFT JOIN (
                SELECT
                    lot_id,
                    SUM(quantity) as qty_picked
                FROM
                    pick_activities
                WHERE
                    synced_with_live_inventory = 0
                GROUP BY
                    lot_id
            ) pa ON (i.lot_id = pa.lot_id)
            WHERE
                i.number_of_units_available > 0
            ORDER BY
                lh.display_location ASC";

        return Yii::app()->rodb->createCommand($sql)->queryAll();
    }

    public static function lotsExpiringSoon()
    {

        $sql =
            'SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ i.lot_id, 
            s.`name` AS supplier,
            p.`product_id`,
            p.`name` AS product_name,
            i.`number_of_units_available`,
            i.`Expiry_date` 
            FROM inventory i
            LEFT JOIN products p ON i.`product_id` = p.`product_id`
            LEFT JOIN suppliers s ON s.`supplier_id` = p.`supplier_id`
            WHERE i.`Expiry_date` >= date(now())
            AND i.`number_of_units_available` > 0
            ORDER BY i.expiry_date ASC';

        $data = Yii::app()->rodb->createCommand($sql)->queryAll(true);

        array_unshift($data, [
            'Lot ID',
            'Supplier name',
            'Product ID',
            'Product name',
            'Num. of units available',
            'Expiration date',
        ]);

        return $data;
    }

    public static function trialUserFunnelPerReferralCampaign($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        return Yii::app()->rodb->createCommand(
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ logs.*,
                users_data.users_created as started_signing_up,
                users_data.signups_count as signups,
                users_data.subscribed_count as conversions,
                users_data.signups_count / logs.views * 100 AS signup_rate,
                users_data.subscribed_count / users_data.signups_count * 100 as conversion_rate
            FROM (
                SELECT
                    DATE(date_created_at) as view_date,
                    WEEK(date_created_at, 3) as iso_week,
                    SUM(IF(action_count >= 1, 1, 0)) AS views,
                    campaign_referral
                FROM
                    (
                    SELECT
                        ip,
                        COUNT(DISTINCT ACTION) AS action_count,
                        date(created_at) AS date_created_at,
                        campaign_referral
                    FROM
                        `trial_logs`
                    WHERE
                        success = 1 AND trial_log_id > 2556 AND 
                        created_at BETWEEN CONCAT(:FROM, ' 00:00:00') AND CONCAT(:TO, ' 23:59:59') AND
                        campaign_referral IS NOT NULL
                    GROUP BY
                        ip, campaign_referral, date(created_at)
                ) action_ip
                GROUP BY
                    DATE(date_created_at), campaign_referral
            ) as logs
            LEFT JOIN (
                SELECT 
                    DATE(u.created) AS date_created_at,
                    COUNT(DISTINCT u.user_id) AS users_created,
                    SUM(IF(s.subscription_id IS NOT NULL, 1, 0)) AS signups_count,
                    SUM(IF(stc.has_subscribed IS NOT NULL OR (s.type != :SUBSCRIPTION_TYPE_TRIAL AND s.active = 1), 1, 0)) as subscribed_count,
                    campaign_referral
                FROM
                    users u
                LEFT JOIN
                    subscriptions s ON s.user_id = u.user_id
                LEFT JOIN (
                    SELECT 
                        subscription_id as has_subscribed
                    FROM
                        subscription_state_changes  
                    WHERE 
                        old_state = 13 AND new_state NOT IN (6,12)
                    GROUP BY
                        subscription_id
                ) stc ON stc.has_subscribed = s.subscription_id
                WHERE
                    u.created BETWEEN CONCAT(:FROM, ' 00:00:00') AND CONCAT(:TO, ' 23:59:59') AND 
                    u.campaign_referral IS NOT NULL
                GROUP BY
                    DATE(u.created), campaign_referral      
            ) as users_data ON users_data.date_created_at = logs.view_date AND users_data.campaign_referral = logs.campaign_referral
            ORDER BY
                logs.view_date DESC
        ")->bindValues([
            ':FROM' => $params['start'],
            ':TO' => $params['end'],
            ':SUBSCRIPTION_TYPE_TRIAL' => Subscriptions::TYPE_TRIAL,
        ])->queryAll();
        
    }

    public static function LufaFarmsFoundation_DonationsReceived($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $data = Yii::app()->rodb->createCommand(
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(transactions.created_time) AS 'Date',
                transactions.transaction_id AS 'Transaction ID',
                transactions.user_id AS 'User ID',
                users.first_name AS 'First Name',
                users.last_name AS 'Last Name',
                users.user_email AS 'Email address',
                CONCAT(users.address, ',', users.zip_code, ',', users.city_name) AS 'Address',
                transactions.type AS 'Transaction Type ID',
                '' AS 'Transaction Type Name',
                transactions.order_id AS 'Order ID',
                transactions.bank_transaction_id AS 'Bank transaction ID',
                ABS(transactions.amount) AS 'Amount transfered'
            FROM 
                transactions 
            LEFT JOIN users ON (users.user_id = transactions.user_id)
            WHERE 
                transactions.type IN (:TYPE_BASKET_DONATION_BY_CC, :TYPE_ONETIME_DONATION_BY_CC, :TYPE_REMOVE_CREDITS_INSTANT_DONATION_GIVEN, :TYPE_WEEKLY_ORDER_DONATION_GIVEN, :TYPE_YEARLY_GIVEBACK_DONATION_GIVEN) AND 
                DATE(transactions.created_time) BETWEEN :FROM AND :TO AND 
                finalized = 1
        ")->bindValues([
            ':FROM' => $params['start'],
            ':TO' => $params['end'],
            ':TYPE_BASKET_DONATION_BY_CC' => Transactions::TYPE_BASKET_DONATION_BY_CC,
            ':TYPE_ONETIME_DONATION_BY_CC' => Transactions::TYPE_ONETIME_DONATION_BY_CC,
            ':TYPE_REMOVE_CREDITS_INSTANT_DONATION_GIVEN' => Transactions::TYPE_REMOVE_CREDITS_INSTANT_DONATION_GIVEN,
            ':TYPE_WEEKLY_ORDER_DONATION_GIVEN' => Transactions::TYPE_WEEKLY_ORDER_DONATION_GIVEN,
            ':TYPE_YEARLY_GIVEBACK_DONATION_GIVEN' => Transactions::TYPE_YEARLY_GIVEBACK_DONATION_GIVEN,
        ])->queryAll();

        $t = new Transactions(); 

        foreach ($data as $key => &$value) {
            $value['Transaction Type Name'] = Yii::t('transactions', $t->getTypes($value['Transaction Type ID']), [], null, 'en');
        }

        return $data;
        
    }

    public static function LufaFarmsFoundation_CreditsGivenToMembers($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $data = Yii::app()->rodb->createCommand(
            "SELECT 
                /*+ MAX_EXECUTION_TIME(900000) */ DATE(transactions_out.created_time) AS 'Date',
                transactions_out.user_id AS 'From_user (donation pool)',
                transactions_with_order_and_client_info.user_id AS 'To_user (User ID)',
                users.first_name AS 'First Name',
                users.last_name AS 'Last Name',
                users.user_email AS 'Email address',
                companies.name AS 'Organisation',
                transactions_with_order_and_client_info.order_id AS 'Order ID',
                ABS(transactions_out.amount) AS 'Amount transfered'
            FROM 
                transactions transactions_with_order_and_client_info
            LEFT JOIN transactions transactions_out ON (transactions_with_order_and_client_info.child_transaction_id = transactions_out.transaction_id AND transactions_out.type = :TYPE_DONATION_TRANSFERED)
            LEFT JOIN users ON (users.user_id = transactions_with_order_and_client_info.user_id)
            LEFT JOIN orders ON (orders.order_id = transactions_with_order_and_client_info.order_id)
            LEFT JOIN companies ON (companies.company_id = orders.company_id_associated)
            WHERE 
                transactions_with_order_and_client_info.type = :TYPE_ADD_CREDITS_DONATION_RECEIVED_FROM_POOL AND 
                DATE(transactions_with_order_and_client_info.created_time) BETWEEN :FROM AND :TO AND 
                transactions_with_order_and_client_info.finalized =1
        ")->bindValues([
            ':FROM' => $params['start'],
            ':TO' => $params['end'],
            ':TYPE_ADD_CREDITS_DONATION_RECEIVED_FROM_POOL' => Transactions::TYPE_ADD_CREDITS_DONATION_RECEIVED_FROM_POOL,
            ':TYPE_DONATION_TRANSFERED' => Transactions::TYPE_DONATION_TRANSFERED,
        ])->queryAll();

        return $data;
        
    }

    public static function portioningCostByProduct($params)
    {
        if(empty($params['start'] || $params['end'])){
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
                    /*+ MAX_EXECUTION_TIME(900000) */ DATE_FORMAT(t.finished_at, '%Y-%m-%d') AS 'Date',
                    ttp.product_id AS 'Product Id',
                    p.name AS 'Product Name (Fr)',
                    p_lan.l_name AS 'Product Name (En)',
                    s.name AS 'Supplier Name (Fr)',
                    s_lan.l_name AS 'Supplier Name (En)',
                    psg.l_name AS 'Portioning Speed Group',
                    ppg.packaging_name AS 'Packaging Type (Fr)',
                    ppgl.packaging_name_lang AS 'Packaging Type (En)',
                    IF(p.manipulation_needed = :daily_portioning, 'Yes', 'No') AS 'Daily Portioning',
                    SUM(ttp.quantity_portioned) AS 'Qty. Portioned',
                    ROUND(SUM(TIMESTAMPDIFF(SECOND, t.started_at, t.finished_at)) / 3600, 2) AS 'Total Portioning Time (Hour)',
                    ROUND((SUM(TIMESTAMPDIFF(SECOND, t.started_at, t.finished_at)))/(SUM(ttp.quantity_portioned)), 2) AS 'Portioning Efficiency (Secs/Portion)',
                    /* $18 IS PORTIONING COST PER HOUR */
                    ROUND(18*(SUM(TIMESTAMPDIFF(SECOND, t.started_at, t.finished_at))/3600), 2) AS 'Actual Portioning Cost ($)',
                    ROUND((18*(SUM(TIMESTAMPDIFF(SECOND, t.started_at, t.finished_at))/3600) + (ppg.packaging_price * SUM(ttp.quantity_portioned))), 2) AS 'Total Cost (Actual + Packaging) ($)',
                    ROUND((18*(SUM(TIMESTAMPDIFF(SECOND, t.started_at, t.finished_at))/3600) + (ppg.packaging_price * SUM(ttp.quantity_portioned)))/ SUM(ttp.quantity_portioned), 2) AS 'Cost ($)/ Portion'
                
                FROM `task_type_portioning` AS ttp

                INNER JOIN products AS p ON ttp.product_id = p.product_id
                INNER JOIN productsLang AS p_lan ON p.product_id = p_lan.product_id
                INNER JOIN portioning_speed_group_lang AS psg ON p.portioning_speed_group_id = psg.portioning_speed_group_id
                INNER JOIN task AS t ON ttp.task_id = t.task_id
                INNER JOIN portioning_packaging AS ppg ON p.portioning_packaging_id = ppg.portioning_packaging_id
                INNER JOIN portioning_packaging_lang AS ppgl ON ppg.portioning_packaging_id = ppgl.portioning_packaging_id
                INNER JOIN suppliers AS s ON s.supplier_id = p.supplier_id
                INNER JOIN suppliersLang AS s_lan ON s_lan.supplier_id = s.supplier_id
                
                WHERE 
                    ttp.quantity_portioned IS NOT NULL 
                    AND t.finished_at >= :start
                    AND t.finished_at < DATE_ADD(:end, INTERVAL 1 DAY)
                
                GROUP BY
                    ttp.product_id
                ORDER BY
                    t.finished_at ASC";
        $data = Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':daily_portioning' => Products::MANIPULATION_NEEDED_URGENT,
            ])->queryAll();                
        return $data;
    }

    public static function trialConversionsReport($params) {
        if(empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $data = Yii::app()->rodb->createCommand(
            "SELECT 
                delivery_date as 'Delivery Date',
                CONCAT(SUBSTRING(YEARWEEK(delivery_date, 6), 1, 4), ' W', SUBSTRING(YEARWEEK(delivery_date, 6), 5, 6)) AS 'Week',
                orders_shipped_total as 'Orders shipped',
                orders_from_trial_shipped as 'Trial Conversions Shipped',
                CONCAT(ROUND((orders_from_trial_shipped) / (orders_shipped_total) * 100, 2), '%') as 'Shipped %',
                ROUND((gross_revenue) / orders_shipped_total,2) as 'ABP',
                ROUND((trial_revenue) / orders_from_trial_shipped,2) as 'Trial Conversions ABP',
                CONCAT(ROUND((trial_revenue / orders_from_trial_shipped) / (gross_revenue / orders_shipped_total) * 100, 2), '%') as 'ABP %',
                ROUND(gross_revenue,2) as 'Gross Revenue',
                ROUND(trial_revenue,2) as 'Trial Conversions Gross Rev.',
                CONCAT(ROUND((trial_revenue) / (gross_revenue) * 100, 2), '%') as 'Rev %'
            FROM (
                SELECT 
                    o.delivery_date as delivery_date,
                    COUNT(o.order_id) as orders_generated,
                    SUM(IF(o.type = 0 AND o.subscription_type = 0, 1, 0)) as automated_orders,
                    SUM(IF(o.type = 1 AND o.subscription_type = 1, 1, 0)) as activation_required_orders,
                    SUM(IF(o.type = 2, 1, 0)) as ondemand_orders,
                    SUM(IF(o.type = 3, 1, 0)) as gift_orders,
                    SUM(IF(o.type = 4, 1, 0)) as trial_orders,
                    SUM(IF(o.status = 5 AND o.subscription_type = 0, 1, 0)) as automated_orders_canceled,
                    SUM(IF(o.status = 5 AND o.subscription_type = 1, 1, 0)) as activation_required_orders_canceled,
                    SUM(IF(o.status = 8 AND o.subscription_type = 1, 1, 0)) as activation_required_orders_never_activated,
                    SUM(IF(o.status = 6 AND o.subscription_type = 0, 1, 0)) as automated_orders_declined,
                    SUM(IF(o.status = 6 AND o.subscription_type = 1, 1, 0)) as activation_required_orders_declined,
                    SUM(IF(o.status = 4 AND o.subscription_type = 0, 1, 0)) as automated_orders_shipped,
                    SUM(IF(o.status = 4 AND o.subscription_type = 1, 1, 0)) as activation_required_orders_shipped,
                    SUM(IF(o.status = 4, 1, 0)) as orders_shipped_total,
                    SUM(IF(o.status = 4, IF(o.type = 1, 1, 0), 0)) as orders_opt_in_shipped,
                    SUM(IF(o.status = 4, IF(o.type = 3, 1, 0), 0)) as gift_orders_shipped,
                    SUM(IF(o.status = 4, IF(o.type = 4, 1, 0), 0)) as trial_orders_shipped,
                    SUM(IF(o.status IN (1,2,4), IF(o.number_of_customization > 0, 1, 0), 0)) as orders_customized,
                    SUM(IF(o.status = 4 AND o.subscription_type = 0, (o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount-o.donation_amount-o.total_consigne_amount), 0)) as automated_gross_revenue,
                    SUM(IF(o.status = 4 AND o.subscription_type = 1, (o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount-o.donation_amount-o.total_consigne_amount), 0)) as activation_required_gross_revenue,
                    SUM(IF(o.status = 4, (o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount-o.donation_amount-o.total_consigne_amount), 0)) as gross_revenue,
                    SUM(IF(o.status = 4 AND o.type != 4, (o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount-o.donation_amount-o.total_consigne_amount), 0)) as gross_revenue_without_trial,
                    SUM(IF(o.type = 3 AND o.status = 4, (o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount-o.donation_amount-o.total_consigne_amount), 0)) as gross_revenue_gift,
                    SUM(o.discount) as discount,
                    SUM(IF(stc.has_subscribed IS NOT NULL AND o.type != 4 AND o.status = 4, (o.total_order_amount-o.total_national_tax-o.total_provincial_tax+o.discount-o.delivery_service_amount-o.donation_amount-o.total_consigne_amount), 0)) as trial_revenue,
                            SUM(IF(stc.has_subscribed IS NOT NULL AND o.type != 4 AND o.status = 4, 1, 0)) as orders_from_trial_shipped,
                    SUM(IF(stc.has_subscribed IS NOT NULL AND o.type != 4 AND o.status = 4, (o.discount), 0)) as trial_discount
                FROM 
                    orders o
                LEFT JOIN 
                    subscriptions s ON s.user_id = o.user_id
                LEFT JOIN 
                (
                    SELECT 
                    subscription_id as has_subscribed
                    FROM
                    subscription_state_changes  
                    WHERE 
                    old_state = 13 AND new_state NOT IN (6,12)
                    GROUP BY
                    subscription_id
                ) stc ON stc.has_subscribed = s.subscription_id
                WHERE 
                    o.delivery_date BETWEEN :start AND :end
                GROUP BY 
                    o.delivery_date
            ) tmp
            GROUP BY 
                delivery_date
            ORDER BY 
                delivery_date DESC
        ")->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end']
        ])->queryAll();

        return $data;
    }

    public static function supplierPurchasing($params) {
        if(empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $data = Yii::app()->rodb->createCommand(
            "SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */ subquery.supplier_id AS 'Supplier ID',
            subquery.name AS 'Supplier Name',
            purch_acc_manager.purch AS 'Purchaser',
            purch_acc_manager.acc_man AS 'Category Manager',
            CONCAT(SUBDATE(:start, INTERVAL 1 YEAR), ' to ', SUBDATE(:end, INTERVAL 1 YEAR)) AS 'LY Period',
            subquery.ly_amount AS 'LY Total $ Purchases',
            CONCAT(:start, ' to ', :end) AS 'TY Period',
            subquery.ty_amount AS 'TY Total $ Purchases',
            ROUND(subquery.ty_amount - subquery.ly_amount, 2) AS 'Variance $',
            IFNULL(ROUND((subquery.ty_amount - subquery.ly_amount)  / subquery.ly_amount  * 100, 2), '') AS 'Variance %'
        FROM (
            SELECT 
                s.supplier_id,
                s.name,
                ROUND(IFNULL(SUM(ly.amount), 0), 2) AS ly_amount,
                ROUND(IFNULL(SUM(ty.amount), 0), 2) AS ty_amount
            FROM 
                suppliers s
            LEFT JOIN (
                SELECT 
                    supplier_id,
                    SUM(amount) AS amount
                FROM (
                    SELECT 
                        s.supplier_id, 
                        sfo.supplier_forecast_orders_id,
                        IF(sfo.reception_type = 3 OR sfo.reception_type = 4,
                            IF(sfo.reception_type = 3,
                                price_per_unit/product_weight * 1000 * sfo.net_weight_received + SUM(ROUND(price_per_unit/product_weight * 1000 * IFNULL(inva.net_weight,0),2)),
                                sfo.reception_cost * IF(invoiced_by=0 AND number_of_units_ordered>quantity_accepted, quantity_accepted, number_of_units_ordered)
                            ),
                            IF(invoiced_by=0 AND number_of_units_ordered<quantity_accepted,
                                price_per_unit * number_of_units_ordered,
                                price_per_unit * quantity_accepted
                            )
                        ) * IF(sfo.status=11, -1 , 1 ) AS 'amount'
                    FROM 
                        supplier_forecast_orders sfo
                    INNER JOIN products p ON (sfo.product_id = p.product_id)
                    INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
                    LEFT JOIN inventory inv ON (inv.supplier_forecast_orders_id = sfo.supplier_forecast_orders_id)
                    LEFT JOIN (
                        SELECT 
                            net_weight, 
                            quantity, 
                            lot_id, 
                            invoice_number 
                        FROM 
                            inventory_activity 
                        WHERE 
                            inventory_activity_type_id IN (300, 301, 302, 303) AND to_be_credited_by_supplier = 1
                    ) inva ON (inva.lot_id = inv.lot_id AND IF(sfo.paid = 1, IF(sfo.lufa_invoice_number = inva.invoice_number,1,0),1) = 1)
                    WHERE 
                        sfo.status IN (4) AND 
                        sfo.delivery_date BETWEEN :start AND :end
                    GROUP BY 
                        sfo.supplier_forecast_orders_id
                ) tmp_ty
                GROUP BY 
                    supplier_id
            ) ty ON (ty.supplier_id = s.supplier_id) 
            LEFT JOIN (
                SELECT 
                    supplier_id,
                    SUM(amount) AS amount
                FROM (
                    SELECT 
                        s.supplier_id, 
                        sfo.supplier_forecast_orders_id,
                        IF(sfo.reception_type = 3 OR sfo.reception_type = 4,
                            IF(sfo.reception_type = 3,
                                price_per_unit/product_weight * 1000 * sfo.net_weight_received + SUM(ROUND(price_per_unit/product_weight * 1000 * IFNULL(inva.net_weight,0),2)),
                                sfo.reception_cost * IF(invoiced_by=0 AND number_of_units_ordered>quantity_accepted, quantity_accepted, number_of_units_ordered)
                            ),
                            IF(invoiced_by=0 AND number_of_units_ordered<quantity_accepted,
                                price_per_unit * number_of_units_ordered,
                                price_per_unit * quantity_accepted
                            )
                        ) * IF(sfo.status=11, -1 , 1 ) AS 'amount'
                    FROM 
                        supplier_forecast_orders sfo
                    INNER JOIN products p ON (sfo.product_id = p.product_id)
                    INNER JOIN suppliers s ON (s.supplier_id = p.supplier_id)
                    LEFT JOIN inventory inv ON (inv.supplier_forecast_orders_id = sfo.supplier_forecast_orders_id)
                    LEFT JOIN (
                        SELECT 
                            net_weight, 
                            quantity, 
                            lot_id, 
                            invoice_number 
                        FROM 
                            inventory_activity 
                        WHERE 
                            inventory_activity_type_id IN (300, 301, 302, 303) AND to_be_credited_by_supplier = 1
                    ) inva ON (inva.lot_id = inv.lot_id AND IF(sfo.paid = 1, IF(sfo.lufa_invoice_number = inva.invoice_number,1,0),1) = 1)
                    WHERE 
                        sfo.status IN (4) AND 
                        sfo.delivery_date BETWEEN SUBDATE(:start, INTERVAL 1 YEAR) AND SUBDATE(:end, INTERVAL 1 YEAR)
                    GROUP BY 
                        sfo.supplier_forecast_orders_id
                ) tmp_ly 
                GROUP BY 
                    supplier_id
            ) ly ON (ly.supplier_id = s.supplier_id) 
            GROUP BY 
                s.supplier_id
            HAVING 
                ROUND(IFNULL(SUM(ty.amount), 0), 2) > 0 OR ROUND(IFNULL(SUM(ly.amount), 0), 2) > 0  
            ) subquery
        LEFT JOIN (
            SELECT 
                s.supplier_id,
                GROUP_CONCAT(DISTINCT CONCAT(purch.first_name, ' ', purch.last_name)) AS purch,
                GROUP_CONCAT(DISTINCT CONCAT(acc_manager.first_name, ' ', acc_manager.last_name)) AS acc_man
            FROM
                suppliers s 
            INNER JOIN products p ON (s.supplier_id = p.supplier_id AND p.status = 1)
            LEFT JOIN productSubSubCategories pssc ON (p.sub_sub_id = pssc.sub_sub_id)
            LEFT JOIN productSubCategories psc ON (psc.subcategory_id = pssc.subcategory_id)
            LEFT JOIN users purch ON (purch.user_id = p.purchaser_id AND purch.user_id != 2)
            LEFT JOIN users acc_manager ON (acc_manager.user_id = p.category_manager_id AND acc_manager.user_id != 2)
            GROUP BY 
                s.supplier_id
        ) purch_acc_manager ON (purch_acc_manager.supplier_id = subquery.supplier_id)
        ")->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end']
        ])->queryAll();

        return $data;
    }


    public static function promoDiscountPerProductsAndDate($params) {
        if(is_array($params['year_month'])) {
            $params['year_month'] = $params['year_month']['id'];
        }
    
        $start_date = $params['year_month'].'-01';
        $end_date = date("Y-m-t", strtotime($start_date));

        $data = Yii::app()->rodb->createCommand(
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ orders.delivery_date AS 'Delivery Date',
                p.product_id AS 'Product ID',
                p.name as 'Product Name',
                s.name as 'Supplier',
                CONCAT(category_manager.first_name, ' ', category_manager.last_name) AS 'Category Manager',
                CONCAT(purch.first_name, ' ', purch.last_name) AS 'Purchaser',
                ROUND((p.price-p.purchasing_price)/p.price*100,2) AS 'Default Margin %',
                pssc.name as 'Sub sub cat.',
                psc.name as 'Sub cat.',
                pc.name as 'Category',
                COUNT(p.product_id) as 'Quantity Sold',
                SUM(IF(default_retail_price_per_unit = defined_retail_price_per_unit_for_default_weight, 1, 0)) as 'Organic Sold',
                ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) as 'Revenue Generated',
                ROUND(SUM(default_retail_price_per_unit - defined_retail_price_per_unit_for_default_weight), 2) as 'Discount Given',
                ROUND(SUM(purchase_price_per_unit), 2) as 'Purchasing price', 
                ROUND(IF(COUNT(p.product_id) > 0, ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) / COUNT(p.product_id), 0), 2) as 'Average Price',
                ROUND(IF(ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) > 0,(ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) - ROUND(SUM(purchase_price_per_unit), 2))/ ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) * 100,0), 2) as 'Average Margin',
                IF((SUM(defined_retail_price_per_unit_for_default_weight) + SUM(default_retail_price_per_unit - defined_retail_price_per_unit_for_default_weight)) > 0, ROUND((SUM(defined_retail_price_per_unit_for_default_weight) + SUM(default_retail_price_per_unit - defined_retail_price_per_unit_for_default_weight) - ROUND(SUM(purchase_price_per_unit), 2)) / (SUM(defined_retail_price_per_unit_for_default_weight) + SUM(default_retail_price_per_unit - defined_retail_price_per_unit_for_default_weight)) * 100 - ROUND(IF( ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) > 0, (ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) - ROUND(SUM(purchase_price_per_unit), 2)) / ROUND(SUM(defined_retail_price_per_unit_for_default_weight), 2) * 100, 0), 2), 2), '-') as 'Margin Loss',
                IF(p.dynamic_pricing = 1, 'Yes', 'No') as 'Dynamic Pricing'
                FROM 
                    order_details od
                RIGHT JOIN products p ON p.product_id = od.product_id
                LEFT JOIN productSubSubCategories pssc ON pssc.sub_sub_id = p.sub_sub_id
                LEFT JOIN productSubCategories psc ON psc.subcategory_id = pssc.subcategory_id
                LEFT JOIN product_categories pc ON pc.category_id = psc.category_id
                LEFT JOIN suppliers s ON s.supplier_id = p.supplier_id
                LEFT JOIN users purch ON (purch.user_id = p.purchaser_id)
                LEFT JOIN users category_manager ON (category_manager.user_id = p.category_manager_id)
                LEFT JOIN orders ON (orders.order_id = od.order_id)
                WHERE 
                    od.order_id IN (SELECT order_id FROM orders WHERE status=4 AND delivery_date BETWEEN :start_date AND :end_date)
                GROUP BY 
                    orders.delivery_date, p.product_id
                HAVING 
                    (SUM(default_retail_price_per_unit - defined_retail_price_per_unit_for_default_weight) > 0 or 'Dynamic Pricing' = 'Yes')
                ORDER BY 
                    p.product_id,
                    orders.delivery_date;")
        ->bindValues([
            'start_date' => $start_date, 
            'end_date' => $end_date,
        ])->queryAll();

        return $data;
    }

    public static function missingItemsPerRoute($params) {
        if(empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $data = Yii::app()->rodb->createCommand(
            "SELECT
                /*+ MAX_EXECUTION_TIME(900000) */ wwr.`date` AS Date,
                IFNULL(companies.name, 'Not set') AS 'Contractor Name',
                `vehicle`.`external_id` AS 'Vehicle Name',
                lbri.departure_time AS 'Scheduled Departure Time',
                `driverComms`.`name` AS 'Driver Name',
                `driverComms`.`phone` AS 'Driver Phone Number',
                SUM(shipped.orders_with_items) AS 'Orders to deliver',
                SUM(refunds.orders_with_an_item_refunded) AS 'Orders with a missing item',
                SUM(shipped.items) AS 'Items to deliver',
                SUM(refunds.items_refunded) AS 'Items reported as missing',
                ROUND(SUM(refunds.amount_refunded),2) AS 'Amount refunded to CX (excluding tax)'
            FROM 
                `ww_routes` wwr 
            LEFT OUTER JOIN `ww_orders` wwo ON (wwo.`route_id`=wwr.`route_id`)  
            LEFT OUTER JOIN orders o ON (wwo.`order_number`=o.`order_id`)  
            LEFT OUTER JOIN `drop_instance` `drop_instance` ON (`drop_instance`.`drop_instance_id`= wwo.`drop_instance_id`) 
            LEFT OUTER JOIN `drop_instance` `hd_di` ON (`hd_di`.`drop_instance_id`= o.`drop_instance_id`) 
            LEFT OUTER JOIN `droppoints` `droppoints` ON (`drop_instance`.`droppoint_id`=`droppoints`.`droppoint_id`) 
            LEFT JOIN (
                SELECT 
                    delivery_date,
                    dp,
                    di,
                    COUNT(DISTINCT order_id) AS orders_with_an_item_refunded,
                    SUM(refund) AS items_refunded,
                    ROUND(SUM(refund_amount),2) AS amount_refunded
                FROM (
                    SELECT 
                        o.delivery_date,
                        IF(dp.hd_product_id IS NULL OR dp.hd_product_id = 0, o.droppoint_id, o.order_id) AS dp,
                        IF(dp.hd_product_id IS NULL OR dp.hd_product_id = 0, o.drop_instance_id, o.order_id) AS di,
                        o.order_id,
                        COUNT(od.order_details_id) refund,
                        SUM(od.refunded_qty*(IF(paid_price_for_real_weight=0, paid_price_per_unit_for_default_weight, paid_price_for_real_weight))) refund_amount
                    FROM 
                        `order_details` od 
                    INNER JOIN orders o ON (o.order_id = od.order_id AND o.delivery_date BETWEEN :start AND :end AND o.status = 4)
                    INNER JOIN droppoints dp ON (dp.droppoint_id = o.droppoint_id)
                    WHERE 
                        od.refunded_reason = 34
                    GROUP BY 
                        o.delivery_date,
                        IF(dp.hd_product_id IS NULL OR dp.hd_product_id = 0, o.droppoint_id, o.order_id),
                        o.order_id
                ) tmp
                GROUP BY 
                    delivery_date,
                    dp
            ) refunds ON (refunds.delivery_date = wwo.delivery_date AND (wwo.order_number = refunds.dp OR refunds.di = wwo.drop_instance_id))
            LEFT JOIN (
                SELECT 
                    delivery_date,
                    dp,
                    di,
                    COUNT(DISTINCT order_id) AS orders_with_items,
                    SUM(items) AS items
                FROM (
                    SELECT 
                        o.delivery_date,
                        IF(dp.hd_product_id IS NULL OR dp.hd_product_id = 0, o.droppoint_id, o.order_id) AS dp,
                        IF(dp.hd_product_id IS NULL OR dp.hd_product_id = 0, o.drop_instance_id, o.order_id) AS di,
                        o.order_id,
                        COUNT(od.order_details_id) items
                    FROM 
                        `order_details` od 
                    INNER JOIN orders o ON (o.order_id = od.order_id AND o.delivery_date BETWEEN :start AND :end AND o.status = 4)
                    INNER JOIN droppoints dp ON (dp.droppoint_id = o.droppoint_id)
                    GROUP BY 
                        o.delivery_date,
                        IF(dp.hd_product_id IS NULL OR dp.hd_product_id = 0, o.droppoint_id, o.order_id),
                        o.order_id
                ) tmp
                GROUP BY 
                    delivery_date,
                    dp
            ) shipped ON (shipped.delivery_date = wwo.delivery_date AND (wwo.order_number = shipped.dp OR shipped.di = wwo.drop_instance_id))
            LEFT OUTER JOIN load_basket_route_info lbri ON (lbri.ww_route_id = wwr.`route_id` /* AND lbri.date = :end */ AND lbri.wave = 1)
            LEFT OUTER JOIN `ww_steps` `ww_steps_all` ON (`ww_steps_all`.`order_id`=wwo.`order_id`) 
            LEFT OUTER JOIN `user_comms` `driverComms` ON (`driverComms`.`user_id`=`ww_steps_all`.`user_id` AND driverComms.main = 1) 
            LEFT JOIN users drivers ON (drivers.user_id = `ww_steps_all`.`user_id`)
            LEFT JOIN companies ON (companies.company_id = drivers.company_id)
            LEFT OUTER JOIN `ww_vehicles` `vehicle` ON (`vehicle`.`vehicle_id`=wwr.`vehicle_id`) 
            WHERE 
                (wwr.date BETWEEN :start AND :end) AND ww_steps_all.step_id IS NOT NULL
            GROUP BY 
                wwr.route_id
            ORDER BY 
                wwr.`date` ASC,
                lbri.departure_time ASC,
                wwr.route_id ASC, 
                ww_steps_all.scheduled_datetime ASC,
                ww_steps_all.order ASC")
        ->bindValues([
            'start' => $params['start'], 
            'end' => $params['end'],
        ])->queryAll();

        return $data;
    }

    // -------------------------------------------------------------------------------- helper_functions
    // --------------------- getGroupByForQuery
    // --------------------- getIdStringFromSelect

    public static function getGroupByForQuery($type, $value, $field)
    {
        $group_by = '';
        switch ($type) {
            case 'default_group_by':
                switch ($value) {
                    case 'Day':
                        $group_by = "DATE({$field})";
                        break;
                    case 'Week':
                        $group_by = "DATE_FORMAT({$field}, '%Y-%V')";
                        break;
                    case 'Month':
                        $group_by = "DATE_FORMAT({$field}, '%Y-%m')";
                        break;
                    case 'Individual Entry':
                        break;
                    default:
                        throw new Exception('Invalid group by value');
                        break;
                }
                break;
            default:
                // code...
                break;
        }

        return $group_by;
    }

    // unpack array of string ["id: garbage","id: garbage","id: garbage"]
    public static function getIdStringFromSelect($array)
    {
        $ids_string = '';
        foreach ($array as $value) {
            $quoted_value = Yii::app()->db->pdoInstance->quote($value['id']);
            $ids_string .= "{$quoted_value},";
        }
        $ids_string = substr($ids_string, 0, -1);

        return $ids_string;
    }

    public static function lufaFarmsDailyMoodChecker($params)
    {
        
        $sql =
            "SELECT 
                created_at as DateSubmitted,
            CASE 
                WHEN channel_id = 1 THEN 'Corporate Time Tracking tool' 
                WHEN channel_id = 2 THEN 'Punch system in DC' 
                WHEN channel_id = 3 THEN 'Punch system in GH' 
                ELSE 'Root'
            END as Channel,
            response_content as Response
        FROM
            eh_employee_mood_responses
        WHERE
            created_at BETWEEN :start AND :end 
        ORDER BY response_id";

        $return = Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
            ':lang' => Yii::app()->language,
        ])->queryAll();
        
    }
    public static function lufaFarmsHRSurveyQuestion($params)
    {
        
        $sql =
            "SELECT 
                monthly_question_id as Survey ID,
                created_at as DateSubmitted,
            CASE 
                WHEN channel_id = 1 THEN 'Corporate Time Tracking tool' 
                WHEN channel_id = 2 THEN 'Punch system in DC' 
                WHEN channel_id = 3 THEN 'Punch system in GH' 
                WHEN channel_id = 4 THEN 'Root' 
                ELSE 'All'
            END as Channel,
            answer as Response
        FROM
            eh_monthly_servey_answers
        WHERE
            created_at BETWEEN :start AND :end 
        ORDER BY monthly_servey_answer_id";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':start' => $params['start'],
            ':end' => $params['end'],
        ])->queryAll();
        
    }

    public static function expiryDateChanges($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }
        
        $sql ="SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */  DATE_FORMAT(arl.created_at, '%Y-%m-%d') AS 'Date of change', 
            sfo.supplier_forecast_orders_id as 'SFID', 
            i.lot_id as 'Lot',
            p.name as 'Product Name', 
            sfo.product_id as 'Product ID' , 
            s.name as 'Supplier',
            DATE_FORMAT(arl.new_value, '%Y-%m-%d') as 'New value',
            DATE_FORMAT(arl.old_value, '%Y-%m-%d') as 'Old value',
            u.user_email as 'User'
            FROM 
                active_record_logs arl
            JOIN inventory i ON (i.lot_id = arl.model_id)
            JOIN supplier_forecast_orders sfo ON (sfo.supplier_forecast_orders_id = i.supplier_forecast_orders_id)
            JOIN products p ON (p.product_id = sfo.product_id)
            JOIN suppliers s ON (p.supplier_id = s.supplier_id)
            JOIN users u ON (u.user_id = arl.user_id)
            WHERE 
                field = 'Expiry_date' AND 
                model = 'Inventory' AND 
                action = 'change' AND 
                DATE(arl.created_at) BETWEEN :start AND :end;";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function dailyAvailabilityTracker($params)
    {
        //The report retrive weekly data(Sunday to Satuday) of $params['start'].
        $date = new DateTime();
        $start_date = $date->format('w') == 0 ? $date->format('Y-m-d') : $date->modify('last Sunday')->format('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+6 days', strtotime($start_date)));

        $sql = 
        "SELECT
            p.product_id 'Product ID',
            p.name 'Product name (fr)',
            pl.l_name 'Product name (en)',
            s.name 'Supplier name',
            pc.name 'Category',
            psb.name 'Subcategory',
            CONCAT(category_manager.first_name ,' ', category_manager.last_name) 'Category Manager',
            CONCAT(purchaser.first_name ,' ', purchaser.last_name) 'Purchaser',
            IF(p.dynamic_pricing = 1, 'Yes', 'No') 'Dynamic pricing',
            /*Product Availability */
            MAX(CASE WHEN stats.delivery_date = :start_date THEN stats.availability END) AS 'Avail. Sunday (%)',
            MAX(CASE WHEN stats.delivery_date = DATE_ADD(:start_date, INTERVAL 1 DAY) THEN stats.availability END) AS 'Avail. Monday (%)',
            MAX(CASE WHEN stats.delivery_date = DATE_ADD(:start_date, INTERVAL 2 DAY) THEN stats.availability END) AS 'Avail. Tuesday (%)',
            MAX(CASE WHEN stats.delivery_date = DATE_ADD(:start_date, INTERVAL 3 DAY) THEN stats.availability END) AS 'Avail. Wednesday (%)',
            MAX(CASE WHEN stats.delivery_date = DATE_ADD(:start_date, INTERVAL 4 DAY) THEN stats.availability END) AS 'Avail. Thursday (%)',
            MAX(CASE WHEN stats.delivery_date = DATE_ADD(:start_date, INTERVAL 5 DAY) THEN stats.availability END) AS 'Avail. Friday (%)',
            MAX(CASE WHEN stats.delivery_date = DATE_ADD(:start_date, INTERVAL 6 DAY) THEN stats.availability END) AS 'Avail. Saturday (%)',
            ROUND(AVG(stats.availability), 2) AS 'Total Avg. %',
            /* SFID detail */
            sfids.supplier_forecast_orders_id 'Next SFID',
            sfids.delivery_date 'SFID Delivery Date',
            CASE
                    WHEN sfids.estimate_delivery_time = 0 THEN '12AM to 6AM'
                    WHEN sfids.estimate_delivery_time = 1 THEN '8AM to 6PM'
                    WHEN sfids.estimate_delivery_time = 2 THEN '6PM to 12AM'
                    ELSE sfids.estimate_delivery_time = NULL
                END AS 'Expected Delivery Time',
            
            sfids.start_selling_on_date 'Display on Marketplace starting on',
            sfids.last_day_of_sale 'Last Day Of Sale',
            CASE 
                WHEN sfids.status = :STATUS_NONE THEN 'Aucun'
                WHEN sfids.status = :STATUS_WISHLIST THEN 'Prévision'
                WHEN sfids.status = :STATUS_DISCUSSION THEN 'En cours de négociation'
                WHEN sfids.status = :STATUS_CONFIRMED THEN 'Commande confirmée par le fournisseur'
                WHEN sfids.status = :STATUS_TO_BE_CONFIRMED THEN 'En attente d\'une confirmation du fournisseur'
                WHEN sfids.status = :STATUS_AUTOMATED_ORDER THEN 'À valider - Commande automatique'
                WHEN sfids.status = :STATUS_NOT_CONFIRMED_IN_TIME THEN 'No confirmé à temps'
                WHEN sfids.status = :STATUS_CANCELED_AFTER_CONFIRMATION THEN 'Annulé après confirmation'
                WHEN sfids.status = :STATUS_CANCELED_BY_SUPPLIER THEN 'Annulé par le fournisseur'
                ELSE sfids.status = NULL
            END AS 'SFID status',
            ROUND(sfids.price_per_unit,2) '$ / Unit',
            sfids.number_of_units_ordered '# Units ordered',
            qty_on_hand.qty 'Qty on hand'
        
        FROM
            products p
        INNER JOIN productsLang pl ON
            p.product_id = pl.product_id
        INNER JOIN suppliers s ON
            s.supplier_id = p.supplier_id
        INNER JOIN productSubSubCategories pssb ON
            pssb.sub_sub_id = p.sub_sub_id
        INNER JOIN productSubCategories psb ON
            psb.subcategory_id = pssb.subcategory_id
        INNER JOIN product_categories pc ON
            pc.category_id = psb.category_id
        INNER JOIN users category_manager ON
            category_manager.user_id = p.category_manager_id
        INNER JOIN users purchaser ON
            purchaser.user_id = p.purchaser_id
        INNER JOIN (
            SELECT
                pas.product_id,
                pas.delivery_date,
                ROUND(AVG(IF(pas.qty_available_for_sell > 0, 1, 0)) * 100, 2) AS 'availability'
            FROM product_availability_stats pas
            WHERE
                pas.delivery_date BETWEEN :start_date AND :end_date
            GROUP BY
                pas.product_id,
                pas.delivery_date
        ) stats ON stats.product_id = p.product_id
        LEFT JOIN(
            SELECT
                sfo.product_id,
                sfo.supplier_forecast_orders_id,
                sfo.delivery_date,
                sfo.estimate_delivery_time,
                sfo.start_selling_on_date,
                sfo.last_day_of_sale,
                sfo.status,
                sfo.price_per_unit,
                sfo.number_of_units_ordered
            FROM
                supplier_forecast_orders sfo
            WHERE
                sfo.status NOT IN(:STATUS_ARRIVED, :STATUS_CANCELED_BY_MERCHANDISER, :STATUS_CREDIT, :STATUS_INTERNAL_TRANSFER) AND sfo.delivery_date >= CURDATE()
            GROUP BY
                sfo.product_id
        )sfids ON sfids.product_id = p.product_id
        LEFT JOIN(
            SELECT
                i.product_id,
                SUM(IF(i.number_of_units_available<=0, 0, i.number_of_units_available )) AS qty
            FROM inventory i
        
            GROUP BY
                i.product_id
            
        )qty_on_hand ON qty_on_hand.product_id = p.product_id
        WHERE
            p.group_type = 0
        GROUP BY
            p.product_id
        ";

        return Yii::app()->rodb->createCommand($sql)
        ->bindValues([
            ':start_date' => $start_date,
            ':end_date' =>$end_date,
            ':STATUS_NONE' => SupplierForecastOrders::STATUS_NONE,
            ':STATUS_WISHLIST'  => SupplierForecastOrders::STATUS_WISHLIST,
            ':STATUS_DISCUSSION'  => SupplierForecastOrders::STATUS_DISCUSSION,
            ':STATUS_CONFIRMED'  => SupplierForecastOrders::STATUS_CONFIRMED,
            ':STATUS_CANCELED_BY_MERCHANDISER'  => SupplierForecastOrders::STATUS_CANCELED_BY_MERCHANDISER,
            ':STATUS_TO_BE_CONFIRMED'  => SupplierForecastOrders::STATUS_TO_BE_CONFIRMED,
            ':STATUS_AUTOMATED_ORDER'  => SupplierForecastOrders::STATUS_AUTOMATED_ORDER,
            ':STATUS_NOT_CONFIRMED_IN_TIME'  => SupplierForecastOrders::STATUS_NOT_CONFIRMED_IN_TIME,
            ':STATUS_CANCELED_AFTER_CONFIRMATION'  => SupplierForecastOrders::STATUS_CANCELED_AFTER_CONFIRMATION,
            ':STATUS_CANCELED_BY_SUPPLIER'  => SupplierForecastOrders::STATUS_CANCELED_BY_SUPPLIER,
            'STATUS_ARRIVED' => SupplierForecastOrders::STATUS_ARRIVED,
            ':STATUS_CREDIT'  => SupplierForecastOrders::STATUS_CREDIT,
            'STATUS_INTERNAL_TRANSFER'  => SupplierForecastOrders::STATUS_INTERNAL_TRANSFER,
        ])->queryAll();
    }


    public static function productsSoldWithTempZone($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql ="SELECT 
            /*+ MAX_EXECUTION_TIME(900000) */  
            DATE_FORMAT(ps.date, '%Y-%m-%d') as 'Date',
            DATE_FORMAT(ps.date, '%W') as 'Day', 
            p.product_id as 'Product ID', 
            p.name as 'Product Name', 
            s.name as 'Supplier' , 
            IF(p.flow_through = 0, 'N', 'Y') as 'Flow-Through (Y/N)',
            CASE
                WHEN p.conservation_mode_in_warehouse = 0 THEN 'AMBIENT'
                WHEN p.conservation_mode_in_warehouse = 1 THEN 'FRIDGE_4_DEGREES'
                WHEN p.conservation_mode_in_warehouse = 2 THEN 'FRIDGE_15_DEGREES'
                WHEN p.conservation_mode_in_warehouse = 3 THEN 'FREEZER'
                WHEN p.conservation_mode_in_warehouse = 4 THEN 'OFFSITE_FRIDGE_4_DEGREES'
                WHEN p.conservation_mode_in_warehouse = 5 THEN 'OFFSITE_FREEZER'
                WHEN p.conservation_mode_in_warehouse = 6 THEN 'FRIDGE_8_DEGREES'
                ELSE 'UNKNOWN - WEIRD'
            END AS 'Conservation Mode In Warehouse',
            p.volume as 'Volume',
            p.weight as 'Weight',
            ps.qty_sold as 'Quantity Sold(units)'
            FROM 
                product_stats ps
            JOIN products p on p.product_id = ps.product_id
            JOIN suppliers s on s.supplier_id = p.supplier_id
            WHERE 
                ps.qty_sold > 0 AND
                ps.date BETWEEN :start AND :end;";
        return Yii::app()->rodb->createCommand($sql)->bindValue(':start', $params['start'])->bindValue(':end', $params['end'])->queryAll();
    }

    public static function resolvedTicketsPerAgentNotBelongsToTheirComm($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range params');
        }

        $sql =
            "SELECT
                CONCAT(u.first_name, ' ', u.last_name) AS 'Agent name',
                COUNT(t.ticket_id) AS '# of Resolved Tickets (Not from agent\'s comm)'
            FROM
                tickets t
            LEFT JOIN community_representative_group_details crgd ON (crgd.community_representative_group_id = t.community_representative_group_id AND crgd.user_id = t.agent_id)
            LEFT JOIN users u ON u.user_id = t.agent_id
            WHERE
                t.updated_at BETWEEN :start AND :end
                AND t.status = :STATUS_RESOLVED
                AND crgd.community_representative_group_details_id IS NULL 
                AND t.agent_id NOT IN(90249, 7) 
                AND t.community_representative_group_id IS NOT NULL
            GROUP BY
                t.agent_id
        ";

        return Yii::app()->rodb->createCommand($sql)
        ->bindValues([
            ':start' => $params['start'],
            ':end' =>$params['end'],
            ':STATUS_RESOLVED' => Tickets::STATUS_RESOLVED,
        ])->queryAll();
    }

    public static function rpcBillingReport($params)
    {
        if(is_array($params['year_month'])) {
            $params['year_month'] = $params['year_month']['id'];
        }
    
        $billingFrom = $params['year_month'].'-01';

        print_r($billingFrom);

        $sql = 
            "SELECT
                ri.rpc_invoice_id AS 'Internal Invoice id',
                s.name AS 'Supplier Name',
                IF(ri.total_due_consignment_fee > 0, 'Invoice', 'Credit') AS 'Consignment Billing Type',
                IF(ri.total_due_handling_fee > 0, 'Invoice', 'Credit') AS 'Handling Billing Type',
                CONCAT(ri.billing_from, ' - ', ri.billing_to) AS 'Billing period',
                ri.rpc_handling_fee AS 'RPC Handling Fee',
                ri.rpc_consignment_fee AS 'RPC Consignment Fee',
                CASE
                    WHEN ri.status = 0 THEN 'Pending'
                    WHEN ri.status = 1 THEN 'Paid'
                    ELSE NULL
                END AS 'Billing Status',
                tf.internal_name AS 'RPC Type',
                rid.total_taken AS 'Total taken',
                rid.total_returned AS 'Total returned',
                rid.total_taken - rid.total_returned AS 'In supplier\'s possession',
                ri.total_due_consignment_fee AS 'Total Consignments due amount($)',
                ri.total_due_handling_fee AS 'Total Handling Fee due amount($)'
            FROM
                rpc_invoices ri
            INNER JOIN rpc_invoices_detail rid ON
                rid.rpc_invoice_id = ri.rpc_invoice_id
            INNER JOIN tote_formats tf on tf.tote_format_id = rid.rpc_type_id
            INNER JOIN suppliers s ON s.supplier_id = ri.supplier_id
            WHERE
                ri.billing_from = :BILLING_FROM
            ";
        //     SELECT
        //     ri.rpc_invoice_id AS 'Invoice id',
        //     CONCAT(ri.billing_from, ' - ', ri.billing_to) AS 'Billing period',
        //     ri.rpc_handling_fee AS 'RPC Handling Fee',
        //     ri.rpc_consignment_fee AS 'RPC Consignment Fee',
        //     CASE
        //         WHEN ri.status = 0 THEN 'Pending',
        //         WHEN ri.status = 1 THEN 'Paid',
        //         ELSE NULL
        //     END AS 'Billing Status',
        //     ri.total_due AS 'Total due amount($)',
            
        // FROM
        //     rpc_invoices ri
        // INNER JOIN rpc_invoices_detail rid ON
        //     rid.rpc_invoice_id = ri.rpc_invoice_id
        // INNER JOIN tote_formats tf on tf.tote_format_id = rid.rpc_type_id
        // WHERE
        //     ri.billing_from = '2024-03-01'

        return Yii::app()->rodb->createCommand($sql)->bindValue(':BILLING_FROM', $billingFrom)->queryAll();

    }

    public static function rpcBillingNetsuiteReport($params)
    {
        if(is_array($params['year_month'])) {
            $params['year_month'] = $params['year_month']['id'];
        }
    
        $billingFrom = $params['year_month'].'-01';

        $sql = 
            "SELECT
                CONCAT('RPC-Cons-', LPAD(ri.rpc_invoice_id, 6, '0')) AS 'Invoice id',
                s.netsuite_customer_id AS 'Netsuite Customer ID',
                s.pay_name AS 'Legal Name (buisness)',
                'Deposit for RPC' AS 'Item',
                s.name AS 'Supplier Name',
                IF(ri.total_due_consignment_fee > 0, 'Invoice', 'Credit') AS 'Billing Type',
                DATE_FORMAT(ri.updated_at, '%Y-%m-%d') AS 'Billing period',
                '' AS 'RPC Handling Fees($)',
                ri.rpc_consignment_fee AS 'RPC Consignment Fee($)',
                'Pending' AS 'Billing Status',
                SUM(rid.total_taken) AS 'Total taken',
                SUM(rid.total_returned) AS 'Total returned',
                SUM(rid.total_taken) - SUM(rid.total_returned) AS 'In supplier\'s possession',
                9 AS 'Department ID',
                914 AS 'Account code ID',
                'CA-S-QC' AS 'Tax Code',
                ROUND(ri.total_due_consignment_fee / 1.14975) AS 'Amount before tax',
                ri.total_due_consignment_fee AS 'Total due amount($)'
            FROM
                rpc_invoices ri
            INNER JOIN rpc_invoices_detail rid ON
                rid.rpc_invoice_id = ri.rpc_invoice_id
            INNER JOIN tote_formats tf ON tf.tote_format_id = rid.rpc_type_id
            INNER JOIN suppliers s ON s.supplier_id = ri.supplier_id
            WHERE
                ri.billing_from = :BILLING_FROM
            GROUP BY 
                ri.rpc_invoice_id

            UNION

            SELECT
                CONCAT('RPC-H-', LPAD(ri.rpc_invoice_id, 6, '0')) AS 'Invoice id',
                s.netsuite_customer_id AS 'Netsuite Customer ID',
                s.pay_name AS 'Legal Name (buisness)',
                'RPC cleaning revenues LUFA' AS 'Item',
                s.name AS 'Supplier Name',
                'Invoice' AS 'Billing Type',
                DATE_FORMAT(ri.updated_at, '%Y-%m-%d') AS 'Billing period',
                ri.rpc_handling_fee AS 'RPC Handling Fee($)',
                '' AS 'RPC Consignment Fee($)',
                'Pending' AS 'Billing Status',
                SUM(rid.total_taken) AS 'Total taken',
                SUM(rid.total_returned) AS 'Total returned',
                SUM(rid.total_taken) - SUM(rid.total_returned) AS 'In supplier\'s possession',
                9 AS 'Department ID',
                913 AS 'Account code ID',
                'CA-S-QC' AS 'Tax Code',
                ROUND(ri.total_due_handling_fee / 1.14975) AS 'Amount before tax',
                ri.total_due_handling_fee AS 'Total due amount($)'
            FROM
                rpc_invoices ri
            INNER JOIN rpc_invoices_detail rid ON
                rid.rpc_invoice_id = ri.rpc_invoice_id
            INNER JOIN tote_formats tf ON tf.tote_format_id = rid.rpc_type_id
            INNER JOIN suppliers s ON s.supplier_id = ri.supplier_id
            WHERE
                ri.billing_from = :BILLING_FROM
            GROUP BY 
                ri.rpc_invoice_id

            ";

        return Yii::app()->rodb->createCommand($sql)->bindValue(':BILLING_FROM', $billingFrom)->queryAll();
    }

    public static function lufaFarmsHrPortalAudit($params)
    {
        
        $sql = "SELECT 
                    user.name AS Name,
                    user.internal_employee_id AS 'Employee ID',
                    user.job_title AS 'Job Title',
                    user.department AS Department,
                    user.sub_department AS 'Sub-department',
                    user.location AS Location,
                    user.direct_report AS 'Direct Report',
                    user.absence_approver AS 'Absence Approver',
                    user.salary_approver AS 'Salary Approver',
                    user.phone AS 'Phone number',
                    user.birthday AS 'Birthday',
                    user.address AS 'Address',
                    user.city AS 'City',
                    user.province AS 'Province',
                    user.zip_code AS 'Postal Code',
                    user.ec1_name AS 'Emergency contact 1 First Name, Last Name',
                    user.ec1_relationship AS 'Emergency contact 1 Relationship',
                    user.ec1_phone AS 'Emergency contact 1 Phone number',
                    user.ec2_name AS 'Emergency contact 2 First Name, Last Name',
                    user.ec2_relationship AS 'Emergency contact 2 Relationship',
                    user.ec2_phone AS 'Emergency contact 2 Phone number'
                FROM 
                    (
                        SELECT
                            t.user_id user_id,
                            t.first_name,
                            t.last_name,
                            CONCAT(t.first_name,' ',t.last_name) AS name,
                            t.job_title,
                            t.internal_employee_id,
                            roles.role_id,
                            department.sub_department,
                            department.department,
                            hr_hiring_requests.hr_job_role_id,
                            hr_hiring_requests.hr_hiring_request_id,
                            hr_request_details.location_id,
                            IF(:lang = 'en_us',po_locations.name,po_locations.fr_name) as location,
                            hr_direct_report_role.direct_report,
                            hr_direct_report_role.absence_approver,
                            hr_direct_report_role.salary_approver,
                            IF(t.phone_home, t.phone_home,hr_employee_contacts.phone) AS phone,
                            t.birthday AS birthday,
                            IF(t.address, t.address, hr_info.address_one) AS address,
                            cities.city_name AS city,
                            states.state_name AS province,
                            IF(t.zip_code, t.zip_code, hr_info.postal_code) AS zip_code,
                            CONCAT(emergency_infos.contact1,' ',emergency_infos.contact1_lname) AS ec1_name,
                            emergency_infos.contact1_relationship AS ec1_relationship,
                            emergency_infos.contact1_phone_number AS ec1_phone,
                            CONCAT(emergency_infos.contact2,' ',emergency_infos.contact2_lname) AS ec2_name,
                            emergency_infos.contact2_relationship AS ec2_relationship,
                            emergency_infos.contact1_phone_number AS ec2_phone
                        FROM
                            users AS t
                            LEFT JOIN hr_employee_infos AS hr_info ON t.user_id = hr_info.user_id
                            LEFT JOIN hr_employee_emergency_infos AS emergency_infos ON t.user_id = emergency_infos.user_id
                            LEFT JOIN user_roles AS userRole ON t.user_id = userRole.user_id
                            LEFT JOIN roles ON userRole.role_id = roles.role_id
                            LEFT JOIN hr_hiring_requests ON roles.role_id = hr_hiring_requests.hr_job_role_id
                            LEFT JOIN hr_request_details ON hr_hiring_requests.hr_request_detail_id = hr_request_details.hr_request_detail_id
                            LEFT JOIN po_locations ON hr_request_details.location_id = po_locations.po_location_id 
                            LEFT JOIN cities ON hr_info.city = cities.city_id
                            LEFT JOIN states ON hr_info.province = states.state_id
                            LEFT JOIN (
                                SELECT
                                    temp_departments.department_id AS department_id,
                                    CASE
                                        WHEN :lang = 'en_us' AND dept.department_name IS NULL THEN temp_departments_lang.l_department_name
                                        WHEN :lang = 'en_us' AND dept.department_name IS NOT NULL THEN dept.department_name
                                        WHEN :lang != 'en_us' AND dept.l_department_name IS NULL THEN temp_departments.department_name
                                        WHEN :lang != 'en_us' AND dept.department_name IS NOT NULL THEN dept.l_department_name
                                        ELSE temp_departments_lang.l_department_name
                                    END AS department,
                                    CASE
                                        WHEN :lang = 'en_us' THEN temp_departments_lang.l_department_name
                                        WHEN :lang != 'en_us' THEN temp_departments.department_name
                                        ELSE temp_departments_lang.l_department_name
                                    END AS sub_department

                                FROM
                                    temp_departments
                                LEFT JOIN (
                                    SELECT
                                        departments.department_id,
                                        departments.department_name,
                                        departments.parent_id,
                                        temp_departments_lang.l_department_name
                                    FROM
                                        temp_departments AS departments
                                    LEFT JOIN temp_departments_lang ON
                                        departments.department_id = temp_departments_lang.department_id
                                ) AS dept ON
                                    temp_departments.parent_id = dept.department_id
                                LEFT JOIN temp_departments_lang ON
                                    temp_departments.department_id = temp_departments_lang.department_id
                            ) department ON roles.department_id = department.department_id
                            LEFT JOIN (
                                SELECT
                                    MAX(IF(approver_type = 3 OR approver_type = 4, CONCAT(udr.first_name, ' ', udr.last_name), NULL)) AS direct_report,
                                    MAX(IF(approver_type = 1, CONCAT(ua.first_name, ' ', ua.last_name), NULL)) AS absence_approver,
                                    MAX(IF(approver_type = 2, CONCAT(usa.first_name, ' ', usa.last_name), NULL)) AS salary_approver,
                                    hr.role_id
                                FROM hr_direct_report_role AS hr
                                LEFT JOIN users AS udr ON hr.approver_id = udr.user_id AND (approver_type = 3 OR approver_type = 4)
                                LEFT JOIN users AS ua ON hr.approver_id = ua.user_id AND approver_type = 1
                                LEFT JOIN users AS usa ON hr.approver_id = usa.user_id AND approver_type = 2
                                GROUP BY hr.role_id
                            ) AS hr_direct_report_role ON roles.role_id = hr_direct_report_role.role_id
                            LEFT JOIN (
                                SELECT 
                                    email_contact.user_id,
                                    email_contact.contact AS email,
                                    phone_contact.contact AS phone
                                FROM 
                                    hr_employee_contacts AS email_contact
                                JOIN 
                                    hr_employee_contacts AS phone_contact ON email_contact.user_id = phone_contact.user_id
                                WHERE 
                                    email_contact.contact_type = 'email'
                                    AND phone_contact.contact_type = 'phone'
                            ) AS hr_employee_contacts ON t.user_id = hr_employee_contacts.user_id
                        WHERE
                            t.employee_status = 'Active'
                            AND t.user_id > 2 
                            AND (t.internal_employee_id IS NOT NULL OR t.job_title IS NOT NULL) 
                            AND t.first_name IS NOT NULL 
                            AND t.`last_name` IS NOT NULL
                    ) AS user group by user_id order by user_id";

        return Yii::app()->rodb->createCommand($sql)->bindValues([
            ':lang' => Yii::app()->language,
        ])->queryAll();
        
    }
    
    public static function surveyResponses()
    {
        $date = new DateTime();
        $year = $date->format('Y');
    
        $dynamicTableName = "subscribers_ltv_logs_" . $year;
        $surveyYear = $year;
    
        // Get survey ID
        $surveyId = Yii::app()->db->createCommand()
            ->select('survey_id')
            ->from('survey')
            ->where('year = :year', [':year' => $surveyYear])
            ->queryScalar();
    
        if (!$surveyId) {
            return [];
        }
    
        // SQL to fetch responses
        $sql = "
            SELECT 
                DISTINCT u.user_id AS user_id,
                rsa.response_text, 
                t.name AS tier_name, 
                s.created, 
                d.hd_product_id, 
                d.address, 
                u.language AS lang,
                CASE 
                    WHEN d.hd_product_id IS NOT NULL THEN 'Home Delivery' 
                    ELSE 'Pick-up Point' 
                END AS delivery_type,
                ltv.total_orders_revenue,
                ROUND(user_orders.average_basket_price, 2) AS average_basket_price,
                user_orders.take_rate AS take_rate
            FROM raw_survey_answer rsa
            LEFT JOIN users u ON u.user_id = rsa.user_id
            LEFT JOIN subscriptions s ON s.user_id = u.user_id
            LEFT JOIN tiers t ON t.id = u.tier_id
            LEFT JOIN drop_instance di ON di.drop_instance_id = s.drop_instance_id
            LEFT JOIN droppoints d ON di.droppoint_id = d.droppoint_id
            LEFT JOIN $dynamicTableName ltv ON ltv.user_id = u.user_id AND ltv.iso_week = (SELECT MAX(iso_week) FROM $dynamicTableName)
            LEFT JOIN (
                SELECT 
                    o.user_id AS user_id,
                    COUNT(o.order_id) AS nb_orders,
                    SUM(o.total_order_amount - o.total_national_tax - o.total_provincial_tax - o.total_consigne_amount - o.delivery_service_amount) AS amount_spent_2024,
                    SUM(o.total_order_amount - o.total_national_tax - o.total_provincial_tax - o.total_consigne_amount - o.delivery_service_amount) / COUNT(o.order_id) AS average_basket_price,
                    sil.nb_weeks_with_purchase / sil.nb_weeks_considered AS take_rate
                FROM orders o
                JOIN supervores_incentive_log sil ON sil.user_id = o.user_id
                WHERE 
                    o.status = 4
                    AND o.delivery_date BETWEEN '$surveyYear-01-01' AND CURDATE()
                    AND sil.year = $surveyYear
                    AND sil.iso_week = WEEK(CURDATE())
                GROUP BY o.user_id
            ) AS user_orders ON user_orders.user_id = u.user_id
            WHERE rsa.survey_id = $surveyId;
        ";
    
        $responses = Yii::app()->rodb->createCommand($sql)->queryAll();
    
        if (empty($responses)) {
            return [];
        }
        // initialize headers
        $formattedData = [];
        $header = [
            "User ID", 
            "Tier Name",
            "Lufavore Since",
            "Delivery Preference",
            "Address",
            "Lifetime Spend",
            "ABP",
            "Take Rate",
            "Language"
        ];
    
        // this is added so that in case the question content was updated no new column will be added,
        // it will registered under the same question based on the quesiton_id
        $questionIdToHeader = [];
    
        // Process responses and dynamically create headers
        foreach ($responses as $response) {
            if (!isset($response['response_text'])) {
                continue;
            }
    
            $decodedResponse = json_decode($response['response_text'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }
    
            foreach ($decodedResponse as $res) {
                $questionId = $res['question_id'] ?? null;
                if ($questionId && !array_key_exists($questionId, $questionIdToHeader)) {

                    //fetch question content from the db because in case of an edit to the question,
                    // the json might have different forms of the same question

                    $questionText = Yii::app()->db->createCommand()
                        ->select('l_text')
                        ->from('questions_lang')
                        ->where('question_id = :question_id', [':question_id' => $questionId])
                        ->queryScalar();
    
                    $questionIdToHeader[$questionId] = $questionText ?: "Question $questionId";
                    if (!in_array($questionText, $header)) {
                        $header[] = $questionText;
                    }
                }
            }
        }
    
        $formattedData[] = $header;
    
        // Format the data rows
        foreach ($responses as $index => $response) {
            $rowData = [
                "User ID" => $response['user_id'] ?? 'Unknown',
                "Tier Name" => $response['tier_name'] ?? '',
                "Lufavore Since" => isset($response['created']) ? (new DateTime($response['created']))->format('Y-m-d') : '',
                "Delivery Preference" => $response['delivery_type'] ?? '',
                "Address" => $response['address'] ?? '',
                "Lifetime Spend" => $response['total_orders_revenue'] ?? '',
                "ABP" => $response['average_basket_price'] ?? '',
                "Take Rate" => $response['take_rate'] ?? '',
                "Language" => $response['lang'] ?? ''
            ];
    
            if (isset($response['response_text'])) {
                $decodedResponse = json_decode($response['response_text'], true);
                foreach ($decodedResponse as $res) {
                    $questionId = $res['question_id'] ?? null;
                    if ($questionId && isset($questionIdToHeader[$questionId])) {
                        $responseValue = $res['option_text_raw'] ?? '';
                        $rowData[$questionIdToHeader[$questionId]] = $responseValue;
                    }
                }
            }
    
            $formattedData[] = $rowData;
        }
    
        return $formattedData;
    }
    
    
    public static function OrderSkippingReasons($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        $sql = "SELECT 
        o.delivery_date AS 'Delivery date', 
        CASE
            WHEN o.skip_reason_id = 1 THEN :NOT_ENOUGH_NEEDED_TO_ORDER
            WHEN o.skip_reason_id = 2 THEN :ITEM_NOT_FOUND_ON_MARKETPLACE 
            WHEN o.skip_reason_id = 3 THEN :UNAVAILABLE_TO_RECEIVE
            ELSE 'Unknown Reason'
        END AS 'Cancellation reason',
        COUNT(o.order_id) AS '# orders canceled',
        ROUND(COUNT(o.order_id) /  NULLIF(total_orders_canceled.nb_o_c, 0) * 100, 2) AS '% orders canceled'
        FROM 
            orders o 
        LEFT JOIN (
            SELECT 
                delivery_date, 
                COUNT(*) as nb_o_c
            FROM 
                orders
            WHERE 
                status = 5 AND 
                delivery_date BETWEEN :start AND :end
            GROUP BY 
                delivery_date
        ) total_orders_canceled ON (o.delivery_date = total_orders_canceled.delivery_date)
        WHERE 
            o.status = 5 AND 
            o.delivery_date BETWEEN :start AND :end
        GROUP BY 
            o.delivery_date, 
            o.skip_reason_id";
        $reasons = Orders::getReasons();

        $notEnoughForMinimumText = $reasons[Orders::NOT_ENOUGH_NEEDED_TO_ORDER]['text'];
        $itemNotFoundOnMarketplaceText = $reasons[Orders::ITEM_NOT_FOUND_ON_MARKETPLACE]['text'];
        $unavailableToReceiveText = $reasons[Orders::UNAVAILABLE_TO_RECEIVE]['text'];

        return Yii::app()->rodb
            ->createCommand($sql)
            ->bindValue(':start', $params['start'])
            ->bindValue(':end', $params['end'])
            ->bindValue(':NOT_ENOUGH_NEEDED_TO_ORDER', "I don’t need enough to order a basket")
            ->bindValue(':ITEM_NOT_FOUND_ON_MARKETPLACE', "I couldn’t find what I needed on the Marketplace")
            ->bindValue(':UNAVAILABLE_TO_RECEIVE', "I won’t be available to receive my basket this week")
            ->queryAll();

    }

    public static function vitalityCitrusInventory()
    {
        $sql = "SELECT 
        inv.lot_id AS 'Lot ID',
        inv.product_id AS 'Product ID',
        p.name AS 'Product',
        CASE 
            WHEN p.gift = 1 THEN 'Gift' 
            WHEN p.trial_product = 1 THEN 'Trial' 
            ELSE '-' 
        END AS 'Type',
        inv.quantity AS 'Units available',
        DATEDIFF(CURDATE(), sfid.reception_timestamp) AS 'Days in inventory',
        i.expiry_date AS 'Expiry date'
        FROM inventory_snapshots AS inv
        JOIN products AS p ON p.product_id = inv.product_id
        JOIN inventory i ON i.lot_id = inv.lot_id
        JOIN supplier_forecast_orders sfid ON i.supplier_forecast_orders_id = sfid.supplier_forecast_orders_id
        WHERE p.supplier_id = 736
        AND p.group_type = 0
        AND inv.created = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
        ORDER BY inv.product_id asc;";

        return Yii::app()->rodb->createCommand($sql)->queryAll();;
    }

    public static function averageVolumeOfBisoProducts($params)
    {
        if (empty($params['start']) || empty($params['end'])) {
            throw new Exception('Missing date range values');
        }

        return Yii::app()
            ->rodb
            ->createCommand(
                "SELECT
                    /*+ MAX_EXECUTION_TIME(900000) */
                    o.order_id AS 'Order ID',
                    o.delivery_date AS 'Delivery date',
                    COUNT(DISTINCT ttp.box_number) AS 'Nb. boxes',
                    o.additional_boxes AS 'Add. boxes',
                    SUM(IF(p.product_id = :biso_product_id, quantity, 0)) AS 'Nb. bisos',
                    ambient_frozen_refri.ambient_sum AS 'Nb items Ambient',
                    ambient_frozen_refri.ambient_total_volume AS 'Total volume Ambient items',
                    ambient_frozen_refri.refri_sum AS 'Nb items Refrigerated',
                    ambient_frozen_refri.refri_total_volume AS 'Total volume Refrigerated items',
                    ambient_frozen_refri.frozen_sum AS 'Nb items Frozen',
                    ambient_frozen_refri.frozen_total_volume AS 'Total volume Refrigerated items',
                    ambient_frozen_refri.refri_frozen_count AS 'Nb items Refrigerated/Frozen',
                    ambient_frozen_refri.refri_frozen_total_volume AS 'Total volume Refrigerated/Frozen items',
                    ((SUM(IF(p.product_id = :biso_product_id, quantity, 0)) * :biso_interior_volume)-ambient_frozen_refri.refri_frozen_total_volume) AS 'Remaining volume in biso(s)'
                FROM
                    task_type_prepbasket ttp
                INNER JOIN orders o ON (ttp.order_id = o.order_id)
                INNER JOIN products p ON (ttp.product_id = p.product_id)
                INNER JOIN (
                    SELECT
                        o.order_id,
                        o.delivery_date,
                        SUM(IF(p.conservation_mode_in_basket = 0, 1, 0)) AS 'ambient_sum',
                        SUM(IF(p.conservation_mode_in_basket = 0, p.volume, 0)) AS 'ambient_total_volume',
                        SUM(IF(p.conservation_mode_in_basket = 1, 1, 0)) AS 'refri_sum',
                        SUM(IF(p.conservation_mode_in_basket = 1, p.volume, 0)) AS 'refri_total_volume',
                        SUM(IF(p.conservation_mode_in_basket = 2, 1, 0)) AS 'frozen_sum',
                        SUM(IF(p.conservation_mode_in_basket = 2, p.volume, 0)) AS 'frozen_total_volume',
                        COUNT(
                            CASE
                                WHEN p.conservation_mode_in_basket = 1 THEN 1
                                WHEN p.conservation_mode_in_basket = 2 THEN 1
                            ELSE
                                NULL
                            END
                        ) AS 'refri_frozen_count',
                        SUM(IF(p.conservation_mode_in_basket = 0, 0, p.volume)) AS 'refri_frozen_total_volume'
                    FROM
                        order_details od
                    INNER JOIN orders o ON (o.status = 4 AND o.delivery_date BETWEEN :start AND :end AND o.order_id = od.order_id)
                    INNER JOIN products p ON (p.product_id = od.product_id)
                    GROUP BY
                        o.order_id
                ) ambient_frozen_refri ON (o.order_id = ambient_frozen_refri.order_id)
                GROUP BY
                    o.order_id,
                    o.delivery_date
                ORDER BY
                    'Nb. boxes' ASC,
                    'Nb. bisos' ASC"
            )
            ->bindValues([
                ':biso_product_id' => 3637,
                ':biso_interior_volume' => 15000,
                ':start' => $params['start'],
                ':end' => $params['end']
            ])
            ->queryAll();
    }
}
