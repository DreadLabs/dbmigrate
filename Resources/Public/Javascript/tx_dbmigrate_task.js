Ext.onReady(function () {
	var

		/**
		 * flags if the commit wizard description field was manually edited
		 * 
		 * @param {Boolean}
		 */
		commitWizardDescriptionManualEdited = false,

		/**
		 * 
		 * @param {String}
		 */
		commitWizardSelector = 'form.tx-dbmigrate-repositorymanager-action-commitwizard',

		/**
		 * 
		 * @param {String}
		 */
		commitWizardOptionSelector = '.tx-dbmigrate-repositorymanager-action-commitwizard-option',

		/**
		 * 
		 * @param {String}
		 */
		commitWizardDescriptionSelector = '.tx-dbmigrate-repositorymanager-actionoption textarea[name="description"]',

		/**
		 * @todo: TYPO3.l10n!
		 *
		 * @param {String}
		 */
		commitWizardDescriptionManualChangeConfirmMessage = 'Override manually edited description?',

		/**
		 * streamlines the commit wizard task action
		 * 
		 * @param {Function}
		 */
		streamlineCommitWizardTaskAction = function () {
			var
				isCommitWizard = $$(commitWizardSelector).length > 0;

			if (isCommitWizard) {
				$('typo3-docheader').hide();
				$('typo3-docbody').setStyle({
					top: '0px'
				});
				$('taskcenter-menu').hide();
				$('taskcenter-item').addClassName('fullwidth')
			}
		},

		/**
		 * observes the commit wizard description field
		 * 
		 * @param {Function}
		 */
		observeCommitWizardDescriptionManualChange = function () {
			$$(commitWizardDescriptionSelector).invoke('observe', 'keypress', function (event) {
				commitWizardDescriptionManualEdited = true;
			});
		},

		/**
		 * observes the commit wizard option change
		 * 
		 * @param {Function}
		 */
		observeCommitWizardOptionChange = function () {
			$$(commitWizardOptionSelector).invoke('observe', 'change', function (event) {
				var
					title = this.readAttribute('title'),
					confirmDescriptionOverride = commitWizardDescriptionManualEdited ? confirm(commitWizardDescriptionManualChangeConfirmMessage) : true;

				if (confirmDescriptionOverride) {
					$$(commitWizardDescriptionSelector).invoke('setValue', title);
					commitWizardDescriptionManualEdited = false;
				}
			});
		};

	streamlineCommitWizardTaskAction();

	observeCommitWizardDescriptionManualChange();

	observeCommitWizardOptionChange();
}, this);
