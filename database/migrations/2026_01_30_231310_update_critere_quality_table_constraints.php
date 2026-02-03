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
        // Vérifier si la colonne type existe, sinon l'ajouter
        $columns = \DB::select("SHOW COLUMNS FROM critere_quality LIKE 'type'");
        if (empty($columns)) {
            Schema::table('critere_quality', function (Blueprint $table) {
                $table->string('type')->after('groupe_id');
            });
        }
        
        // Supprimer les enregistrements avec groupe_id NULL, 0 ou invalide, ou type NULL
        $validGroupIds = \DB::table('groupes')->pluck('id')->toArray();
        \DB::table('critere_quality')
            ->where(function($query) use ($validGroupIds) {
                $query->whereNull('groupe_id')
                      ->orWhere('groupe_id', 0)
                      ->orWhereNotIn('groupe_id', $validGroupIds);
            })
            ->orWhereNull('type')
            ->delete();
        
        // Retirer la contrainte unique sur nom si elle existe
        $indexes = \DB::select("SHOW INDEX FROM critere_quality WHERE Column_name = 'nom' AND Non_unique = 0");
        if (!empty($indexes)) {
            $indexName = $indexes[0]->Key_name;
            \DB::statement("ALTER TABLE critere_quality DROP INDEX `{$indexName}`");
        }
        
        // Vérifier et supprimer la clé étrangère si elle existe
        $foreignKeys = \DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'critere_quality' 
            AND COLUMN_NAME = 'groupe_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (!empty($foreignKeys)) {
            $foreignKeyName = $foreignKeys[0]->CONSTRAINT_NAME;
            \DB::statement("ALTER TABLE critere_quality DROP FOREIGN KEY `{$foreignKeyName}`");
        }
        
        Schema::table('critere_quality', function (Blueprint $table) {
            // Rendre groupe_id et type non-nullables
            // On doit d'abord supprimer les colonnes et les recréer
            $table->dropColumn(['groupe_id', 'type']);
        });
        
        Schema::table('critere_quality', function (Blueprint $table) {
            // Recréer les colonnes avec les bonnes contraintes
            $table->foreignId('groupe_id')->after('nom')->constrained('groupes')->onDelete('restrict');
            $table->string('type')->after('groupe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('critere_quality', function (Blueprint $table) {
            // Restaurer la contrainte unique sur nom
            $table->unique('nom');
            
            // Rendre groupe_id et type nullables
            $table->dropForeign(['groupe_id']);
            $table->dropColumn(['groupe_id', 'type']);
        });
        
        Schema::table('critere_quality', function (Blueprint $table) {
            $table->foreignId('groupe_id')->nullable()->after('nom')->constrained('groupes')->onDelete('set null');
            $table->string('type')->nullable()->after('groupe_id');
        });
    }
};
