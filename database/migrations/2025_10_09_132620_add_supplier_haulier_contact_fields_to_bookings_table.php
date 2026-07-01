 <?php                                                                                                                                                                                                                                                   
  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  return new class extends Migration
  {
      /**
       * Run the migrations.
       */
      public function up(): void
      {
          $existingIndexes = Schema::getIndexListing('bookings');

          Schema::table('bookings', function (Blueprint $table) use ($existingIndexes) {
              if (!Schema::hasColumn('bookings', 'supplier')) {
                  $table->string('supplier', 255)->nullable()->after('carrier_id');
              }
              if (!Schema::hasColumn('bookings', 'haulier')) {
                  $table->string('haulier', 255)->nullable()->after('supplier');
              }
              if (!Schema::hasColumn('bookings', 'contact_name')) {
                  $table->string('contact_name', 255)->nullable()->after('haulier');
              }
              if (!Schema::hasColumn('bookings', 'contact_phone')) {
                  $table->string('contact_phone', 50)->nullable()->after('contact_name');
              }

              if (!in_array('bookings_supplier_contact_name_index', $existingIndexes)) {
                  $table->index(['supplier', 'contact_name']);
              }
              if (!in_array('bookings_haulier_contact_name_index', $existingIndexes)) {
                  $table->index(['haulier', 'contact_name']);
              }
          });
      }

      /**
       * Reverse the migrations.
       */
      public function down(): void
      {
          Schema::table('bookings', function (Blueprint $table) {
              $table->dropIndex(['supplier', 'contact_name']);
              $table->dropIndex(['haulier', 'contact_name']);
              $table->dropColumn(['supplier', 'haulier', 'contact_name', 'contact_phone']);
          });
      }
  };
