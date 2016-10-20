module.exports = function(grunt) {

	// Project configuration. 
	grunt.initConfig({
		watch: {},
		concat: {
			commonLibrariesJs: {
				src: [
					'upload/catalog/view/javascript/jquery/jquery-2.1.1.min.js',
					'upload/catalog/view/javascript/bootstrap/js/bootstrap.min.js',
					'upload/catalog/view/javascript/chosen/chosen.jquery.js',
					'upload/catalog/view/javascript/common.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/common/libraries.js',
			},
			commonJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: [
					'upload/catalog/view/theme/default/src/js/app.js',
					'upload/catalog/view/theme/default/src/js/common/**/*.js',
					'upload/catalog/view/theme/default/src/js/shared/tooltips/tooltipsService.js',
					'upload/catalog/view/theme/default/src/js/shared/tooltips/tooltipsController.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/common.js',
			},
			accountDashboardLibrariesJs: {
				src: [
					'upload/catalog/view/javascript/jquery/jquery-2.1.1.min.js',
					'upload/catalog/view/javascript/bootstrap/js/bootstrap.min.js',
					'upload/catalog/view/javascript/chosen/chosen.jquery.js',
					'upload/catalog/view/javascript/chart.js',
					'upload/catalog/view/javascript/jquery.payment.min.js',
					'upload/catalog/view/javascript/jquery/jquery.mask.js',
					'upload/catalog/view/javascript/common.js',
					'upload/catalog/view/theme/default/src/js/checkout.js',
					'upload/catalog/view/theme/default/src/js/account/components/functions.js',
				],
				dest: 'upload/catalog/view/theme/default/dist/js/account/libraries.js',
			},
			accountDashboardJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: [
					'upload/catalog/view/theme/default/src/js/app.js',
					'upload/catalog/view/theme/default/src/js/account/**/*.js',
					'upload/catalog/view/theme/default/src/js/shared/tooltips/tooltipsService.js',
					'upload/catalog/view/theme/default/src/js/shared/tooltips/tooltipsController.js',
					'upload/catalog/view/theme/default/src/js/shared/events/getEventTarget.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/account/dashboard.js',
			},
			affiliateCustomerJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: ['upload/catalog/view/theme/default/src/js/affiliate/customer/*.js'],
				dest: 'upload/catalog/view/theme/default/dist/js/affiliate/customer.js',
			},
			affiliateProfileJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: ['upload/catalog/view/theme/default/src/js/affiliate/profile/*.js'],
				dest: 'upload/catalog/view/theme/default/dist/js/affiliate/profile.js',
			},
			affiliateRegisterJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: ['upload/catalog/view/theme/default/src/js/affiliate/register/*.js'],
				dest: 'upload/catalog/view/theme/default/dist/js/affiliate/register.js',
			},
			csrCheckoutJs: {
				src: [
					'upload/catalog/view/javascript/jquery.payment.min.js',
					'upload/catalog/view/javascript/jquery/jquery.mask.js',
					'upload/catalog/view/theme/default/src/js/checkout.js',
				],
				dest: 'upload/catalog/view/theme/default/dist/js/csr/checkout.js',
			},
			customerJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: ['upload/catalog/view/theme/default/src/js/customer/*.js'],
				dest: 'upload/catalog/view/theme/default/dist/js/customer.js',
			},
			customerKickoffJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: [
					'upload/catalog/view/theme/default/src/js/customer/*.js',
					'upload/catalog/view/theme/default/src/js/customer/kickoff/*.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/customer/kickoff.js',
			},
			customerProfileJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: [
					'upload/catalog/view/theme/default/src/js/customer/*.js',
					'upload/catalog/view/theme/default/src/js/customer/profile/*.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/customer/profile.js',
			},
			customerLoginJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: [
					'upload/catalog/view/theme/default/src/js/customer/*.js',
					'upload/catalog/view/theme/default/src/js/customer/login/*.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/customer/login.js',
			},
			customerRegisterJs: {
				options: {
					banner: '(function () {',
					separator: '})(); (function () {',
					footer: '})();'
				},
				src: [
					'upload/catalog/view/theme/default/src/js/customer/*.js',
					'upload/catalog/view/theme/default/src/js/customer/register/*.js'
				],
				dest: 'upload/catalog/view/theme/default/dist/js/customer/register.js',
			},
			// CSS Concat
			sharedCss: {
				src: [
					'upload/catalog/view/javascript/bootstrap/css/bootstrap.min.css',
					'upload/catalog/view/theme/default/src/less/shared.less',
					'upload/catalog/view/theme/default/src/less/shared/**/*.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/shared.less'
			},
			commonCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/common/common.less',
					'upload/catalog/view/theme/default/src/less/common/header/*.less',
					'upload/catalog/view/theme/default/src/less/common/footer/*.less',
					'upload/catalog/view/theme/default/src/less/common/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/common.less'
			},
			commonHomeCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/common/home/*.less',
				],
				dest: 'upload/catalog/view/theme/default/dist/less/common/home.less'
			},
			accountDashboardCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/checkout.less',
					'upload/catalog/view/theme/default/src/less/account/dashboard/**/*.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/account/dashboard.less'
			},
			affiliateCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/affiliate/affiliate.less',
					'upload/catalog/view/theme/default/src/less/affiliate/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/affiliate.less'
			},
			affiliateDashboardCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/affiliate/affiliate.less',
					'upload/catalog/view/theme/default/src/less/affiliate/dashboard/*.less',
					'upload/catalog/view/theme/default/src/less/affiliate/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/affiliate/dashboard.less',
			},
			affiliateLoginCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/affiliate/affiliate.less',
					'upload/catalog/view/theme/default/src/less/affiliate/login/*.less',
					'upload/catalog/view/theme/default/src/less/affiliate/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/affiliate/login.less',
			},
			affiliateProfileCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/affiliate/affiliate.less',
					'upload/catalog/view/theme/default/src/less/affiliate/profile/*.less',
					'upload/catalog/view/theme/default/src/less/affiliate/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/affiliate/profile.less',
			},
			affiliateRegisterCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/affiliate/affiliate.less',
					'upload/catalog/view/theme/default/src/less/affiliate/register/*.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/affiliate/register.less',
			},
			csrCheckoutCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/checkout.less',
					'upload/catalog/view/theme/default/src/less/csr/checkout/*.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/csr/checkout.less'
			},
			customerCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/customer/customer.less',
					'upload/catalog/view/theme/default/src/less/customer/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/customer.less'
			},
			customerForgottenCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/customer/customer.less',
					'upload/catalog/view/theme/default/src/less/customer/forgotten/*.less',
					'upload/catalog/view/theme/default/src/less/customer/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/customer/forgotten.less',
			},
			customerKickoffCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/customer/customer.less',
					'upload/catalog/view/theme/default/src/less/customer/kickoff/*.less',
					'upload/catalog/view/theme/default/src/less/customer/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/customer/kickoff.less',
			},
			customerProfileCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/customer/customer.less',
					'upload/catalog/view/theme/default/src/less/customer/profile/*.less',
					'upload/catalog/view/theme/default/src/less/customer/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/customer/profile.less',
			},
			customerLoginCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/customer/customer.less',
					'upload/catalog/view/theme/default/src/less/customer/login/*.less',
					'upload/catalog/view/theme/default/src/less/customer/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/customer/login.less',
			},
			customerRegisterCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/customer/customer.less',
					'upload/catalog/view/theme/default/src/less/customer/register/*.less',
					'upload/catalog/view/theme/default/src/less/customer/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/customer/register.less',
			},
			informationCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/information/information.less',
					'upload/catalog/view/theme/default/src/less/information/responsive.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/information/information.less'
			},
			informationContactCss: {
				src: [
					'upload/catalog/view/theme/default/src/less/information/information.less',
					'upload/catalog/view/theme/default/src/less/information/contact/*.less'
				],
				dest: 'upload/catalog/view/theme/default/dist/less/information/contact.less',
			}
		},
		less: {
			shared: {
				files: {
					'upload/catalog/view/theme/default/dist/css/shared.css' : 'upload/catalog/view/theme/default/dist/less/shared.less'
				}
			},
			common: {
				files: {
					'upload/catalog/view/theme/default/dist/css/common.css' : 'upload/catalog/view/theme/default/dist/less/common.less'
				}
			},
			commonHome: {
				files: {
					'upload/catalog/view/theme/default/dist/css/common/home.css' : 'upload/catalog/view/theme/default/dist/less/common/home.less'
				}
			},
			accountDashboard: {
				files: {
					'upload/catalog/view/theme/default/dist/css/account/dashboard.css' : 'upload/catalog/view/theme/default/dist/less/account/dashboard.less'
				}
			},
			affiliate: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate.css' : 'upload/catalog/view/theme/default/dist/less/affiliate.less'
				}
			},
			affiliateDashboard: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/dashboard.css' : 'upload/catalog/view/theme/default/dist/less/affiliate/dashboard.less'
				}
			},
			affiliateLogin: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/login.css' : 'upload/catalog/view/theme/default/dist/less/affiliate/login.less'
				}
			},
			affiliateProfile: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/profile.css' : 'upload/catalog/view/theme/default/dist/less/affiliate/profile.less'
				}
			},
			affiliateRegister: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/register.css' : 'upload/catalog/view/theme/default/dist/less/affiliate/register.less'
				}
			},
			csrCheckout: {
				files: {
					'upload/catalog/view/theme/default/dist/css/csr/checkout.css' : 'upload/catalog/view/theme/default/dist/less/csr/checkout.less'
				}
			},
			customer: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer.css' : 'upload/catalog/view/theme/default/dist/less/customer.less'
				}
			},
			customerForgotten: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/forgotten.css' : 'upload/catalog/view/theme/default/dist/less/customer/forgotten.less'
				}
			},
			customerKickoff: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/kickoff.css' : 'upload/catalog/view/theme/default/dist/less/customer/kickoff.less'
				}
			},
			customerProfile: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/profile.css' : 'upload/catalog/view/theme/default/dist/less/customer/profile.less'
				}
			},
			customerLogin: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/login.css' : 'upload/catalog/view/theme/default/dist/less/customer/login.less'
				}
			},
			customerRegister: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/register.css' : 'upload/catalog/view/theme/default/dist/less/customer/register.less'
				}
			},
			information: {
				files: {
					'upload/catalog/view/theme/default/dist/css/information/information.css' : 'upload/catalog/view/theme/default/dist/less/information/information.less'
				}
			},
			informationContact: {
				files: {
					'upload/catalog/view/theme/default/dist/css/information/contact.css' : 'upload/catalog/view/theme/default/dist/less/information/contact.less'
				}
			},
		},
		uglify: {
			options: {
				mangle: false
			},
			commonLibraries: {
				files: {
					'upload/catalog/view/theme/default/dist/js/common/libraries.min.js': ['upload/catalog/view/theme/default/dist/js/common/libraries.js']
				}
			},
			common: {
				files: {
					'upload/catalog/view/theme/default/dist/js/common.min.js': ['upload/catalog/view/theme/default/dist/js/common.js']
				}
			},
			accountDashboardLibraries: {
				files: {
					'upload/catalog/view/theme/default/dist/js/account/libraries.min.js': ['upload/catalog/view/theme/default/dist/js/account/libraries.js']
				}
			},
			accountDashboard: {
				files: {
					'upload/catalog/view/theme/default/dist/js/account/dashboard.min.js': ['upload/catalog/view/theme/default/dist/js/account/dashboard.js']
				}
			},
			affiliateCustomer: {
				files: {
					'upload/catalog/view/theme/default/dist/js/affiliate/customer.min.js': ['upload/catalog/view/theme/default/dist/js/affiliate/customer.js']
				}
			},
			affiliateProfile: {
				files: {
					'upload/catalog/view/theme/default/dist/js/affiliate/profile.min.js': ['upload/catalog/view/theme/default/dist/js/affiliate/profile.js']
				}
			},
			affiliateRegister: {
				files: {
					'upload/catalog/view/theme/default/dist/js/affiliate/register.min.js': ['upload/catalog/view/theme/default/dist/js/affiliate/register.js']
				}
			},
			csrCheckout: {
				files: {
					'upload/catalog/view/theme/default/dist/js/csr/checkout.min.js': ['upload/catalog/view/theme/default/dist/js/csr/checkout.js']
				}
			},
			customer: {
				files: {
					'upload/catalog/view/theme/default/dist/js/customer.min.js': ['upload/catalog/view/theme/default/dist/js/customer.js']
				}
			},
			customerKickoff: {
				files: {
					'upload/catalog/view/theme/default/dist/js/customer/kickoff.min.js': ['upload/catalog/view/theme/default/dist/js/customer/kickoff.js']
				}
			},
			customerProfile: {
				files: {
					'upload/catalog/view/theme/default/dist/js/customer/profile.min.js': ['upload/catalog/view/theme/default/dist/js/customer/profile.js']
				}
			},
			customerLogin: {
				files: {
					'upload/catalog/view/theme/default/dist/js/customer/login.min.js': ['upload/catalog/view/theme/default/dist/js/customer/login.js']
				}
			},
			customerRegister: {
				files: {
					'upload/catalog/view/theme/default/dist/js/customer/register.min.js': ['upload/catalog/view/theme/default/dist/js/customer/register.js']
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			shared: {
				files: {
					'upload/catalog/view/theme/default/dist/css/shared.min.css': ['upload/catalog/view/theme/default/dist/css/shared.css']
				}
			},
			common: {
				files: {
					'upload/catalog/view/theme/default/dist/css/common.min.css': ['upload/catalog/view/theme/default/dist/css/common.css']
				}
			},
			commonHome: {
				files: {
					'upload/catalog/view/theme/default/dist/css/common/home.min.css': ['upload/catalog/view/theme/default/dist/css/common/home.css']
				}
			},
			accountDashboard: {
				files: {
					'upload/catalog/view/theme/default/dist/css/account/dashboard.min.css': ['upload/catalog/view/theme/default/dist/css/account/dashboard.css']
				}
			},
			affiliate: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate.min.css': ['upload/catalog/view/theme/default/dist/css/affiliate.css']
				}
			},
			affiliateDashboard: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/dashboard.min.css': ['upload/catalog/view/theme/default/dist/css/affiliate/dashboard.css']
				}
			},
			affiliateLogin: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/login.min.css': ['upload/catalog/view/theme/default/dist/css/affiliate/login.css']
				}
			},
			affiliateProfile: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/profile.min.css': ['upload/catalog/view/theme/default/dist/css/affiliate/profile.css']
				}
			},
			affiliateRegister: {
				files: {
					'upload/catalog/view/theme/default/dist/css/affiliate/register.min.css': ['upload/catalog/view/theme/default/dist/css/affiliate/register.css']
				}
			},
			csrCheckout: {
				files: {
					'upload/catalog/view/theme/default/dist/css/csr/checkout.min.css': ['upload/catalog/view/theme/default/dist/css/csr/checkout.css']
				}
			},
			customer: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer.min.css': ['upload/catalog/view/theme/default/dist/css/customer.css']
				}
			},
			customerForgotten: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/forgotten.min.css': ['upload/catalog/view/theme/default/dist/css/customer/forgotten.css']
				}
			},
			customerKickoff: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/kickoff.min.css': ['upload/catalog/view/theme/default/dist/css/customer/kickoff.css']
				}
			},
			customerProfile: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/profile.min.css': ['upload/catalog/view/theme/default/dist/css/customer/profile.css']
				}
			},
			customerLogin:{
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/login.min.css': ['upload/catalog/view/theme/default/dist/css/customer/login.css']
				}
			},
			customerRegister: {
				files: {
					'upload/catalog/view/theme/default/dist/css/customer/register.min.css': ['upload/catalog/view/theme/default/dist/css/customer/register.css']
				}
			},
			information: {
				files: {
					'upload/catalog/view/theme/default/dist/css/information/information.min.css': ['upload/catalog/view/theme/default/dist/css/information/information.css']
				}
			},
			informationContact: {
				files: {
					'upload/catalog/view/theme/default/dist/css/information/contact.min.css': ['upload/catalog/view/theme/default/dist/css/information/contact.css']
				}
			},
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	/* Default Tasks */
	grunt.registerTask('default',['concat','uglify','cssmin','watch']);
	grunt.registerTask('compile',['concat','less','uglify','cssmin']);

	/* Shared Tasks*/
	grunt.registerTask('sharedcss',[
		'concat:sharedCss','less:shared','cssmin:shared'
	]);

	/* Common Tasks*/
	grunt.registerTask('commoncss',[
		'concat:commonCss','less:common','cssmin:common'
	]);

	grunt.registerTask('commonhomecss',[
		'concat:commonHomeCss','less:commonHome','cssmin:commonHome'
	]);

	/* Dashboard Tasks*/
	grunt.registerTask('dashboard',[
		'concat:accountDashboardCss','less:accountDashboard','cssmin:accountDashboard',
		'concat:accountDashboardJs','uglify:accountDashboard'
	]);

	grunt.registerTask('dashboardjs',[
		'concat:accountDashboardJs','uglify:accountDashboard'
	]);
	
	grunt.registerTask('dashboardcss',[
		'concat:accountDashboardCss','less:accountDashboard','cssmin:accountDashboard'
	]);

	/* Affiliate Tasks*/
	grunt.registerTask('affiliatecss',[
		'concat:affiliateCss','less:affiliate','cssmin:affiliate'
	]);

	grunt.registerTask('affiliatedashboardcss',[
		'concat:affiliateDashboardCss','less:affiliateDashboard','cssmin:affiliateDashboard'
	]);

	grunt.registerTask('affiliatelogincss',[
		'concat:affiliateLoginCss','less:affiliateLogin','cssmin:affiliateLogin'
	]);

	grunt.registerTask('affiliateprofilecss',[
		'concat:affiliateProfileCss','less:affiliateProfile','cssmin:affiliateProfile'
	]);

	grunt.registerTask('affiliateregistercss',[
		'concat:affiliateRegisterCss','less:affiliateRegister','cssmin:affiliateRegister'
	]);

	/* CSR Checkout Tasks */
	grunt.registerTask('csrcheckoutcss',[
		'concat:csrCheckoutCss','less:csrCheckout','cssmin:csrCheckout'
	]);

	/* Customer Tasks */
	grunt.registerTask('customercss',[
		'concat:customerCss','less:customer','cssmin:customer'
	]);

	grunt.registerTask('customerforgottencss',[
		'concat:customerForgottenCss','less:customerForgotten','cssmin:customerForgotten'
	]);

	grunt.registerTask('customerkickoffjs',[
		'concat:customerKickoffJs','uglify:customerKickoff'
	]);
	
	grunt.registerTask('customerkickoffcss',[
		'concat:customerKickoffCss','less:customerKickoff','cssmin:customerKickoff'
	]);
	
	grunt.registerTask('customerprofilecss',[
		'concat:customerProfileCss','less:customerProfile','cssmin:customerProfile'
	]);
	
	grunt.registerTask('customerlogincss',[
		'concat:customerLoginCss','less:customerLogin','cssmin:customerLogin'
	]);
	
	grunt.registerTask('customerregistercss',[
		'concat:customerRegisterCss','less:customerRegister','cssmin:customerRegister'
	]);

	/* Information Tasks*/
	grunt.registerTask('informationcss',[
		'concat:informationCss','less:information','cssmin:information'
	]);
	
	grunt.registerTask('informationcontactcss',[
		'concat:informationContactCss','less:informationContact','cssmin:informationContact'
	]);

	/* JS Tasks */
	grunt.registerTask('js',[
		'concat:commonLibrariesJs','uglify:commonLibraries',
		'concat:commonJs','uglify:common',
		'concat:accountDashboardLibrariesJs','uglify:accountDashboardLibraries',
		'concat:accountDashboardJs','uglify:accountDashboard',
		'concat:affiliateCustomerJs','uglify:affiliateCustomer',
		'concat:affiliateProfileJs','uglify:affiliateProfile',
		'concat:affiliateRegisterJs','uglify:affiliateRegister',
		'concat:csrCheckoutJs','uglify:csrCheckout',
		'concat:customerJs','uglify:customer',
		'concat:customerKickoffJs','uglify:customerKickoff',
		'concat:customerProfileJs','uglify:customerProfile',
		'concat:customerRegisterJs','uglify:customerRegister'
	]);

	grunt.registerTask('profilejs',[
		'concat:customerProfileJs','uglify:customerProfile'
	]);

	grunt.registerTask('loginjs',[
		'concat:customerLoginJs','uglify:customerLogin'
	]);

	grunt.registerTask('css',[
		'concat:sharedCss','less:shared','cssmin:shared',
		'concat:commonCss','less:common','cssmin:common',
		'concat:commonHomeCss','less:commonHome','cssmin:commonHome',
		'concat:accountDashboardCss','less:accountDashboard','cssmin:accountDashboard',
		'concat:affiliateCss','less:affiliate','cssmin:affiliate',
		'concat:affiliateLoginCss','less:affiliateLogin','cssmin:affiliateLogin',
		'concat:affiliateProfileCss','less:affiliateProfile','cssmin:affiliateProfile',
		'concat:affiliateRegisterCss','less:affiliateRegister','cssmin:affiliateRegister',
		'concat:commonHomeCss','less:commonHome','cssmin:commonHome',
		'concat:csrCheckoutCss','less:csrCheckout','cssmin:csrCheckout',
		'concat:customerCss','less:customer','cssmin:customer',
		'concat:customerForgottenCss','less:customerForgotten','cssmin:customerForgotten',
		'concat:customerKickoffCss','less:customerKickoff','cssmin:customerKickoff',
		'concat:customerProfileCss','less:customerProfile','cssmin:customerProfile',
		'concat:customerRegisterCss','less:customerRegister','cssmin:customerRegister',
		'concat:informationCss','less:information','cssmin:information',
		'concat:informationContactCss','less:informationContact','cssmin:informationContact'
	]);
};