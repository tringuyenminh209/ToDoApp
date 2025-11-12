package ecccomp.s2240788.mobile_android.ui.activities;

import android.content.Intent;
import android.os.Bundle;
import androidx.appcompat.app.AppCompatActivity;
import ecccomp.s2240788.mobile_android.R;
import ecccomp.s2240788.mobile_android.databinding.ActivitySettingsBinding;

public class SettingsActivity extends BaseActivity {

    private ActivitySettingsBinding binding;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivitySettingsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        setupWindowInsets();

        setupClickListeners();
        setupBottomNavigation();
    }

    private void setupClickListeners() {
        // Back button
        if (binding.btnBack != null) {
            binding.btnBack.setOnClickListener(v -> finish());
        }
    }

    private void setupBottomNavigation() {
        binding.bottomNavigation.setSelectedItemId(R.id.nav_settings);

        binding.bottomNavigation.setOnItemSelectedListener(item -> {
            int itemId = item.getItemId();
            
            if (itemId == R.id.nav_home) {
                startActivity(new Intent(this, MainActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_calendar) {
                startActivity(new Intent(this, CalendarActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_paths) {
                startActivity(new Intent(this, PathsActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_knowledge) {
                startActivity(new Intent(this, KnowledgeActivity.class));
                finish();
                return true;
            } else if (itemId == R.id.nav_settings) {
                // Current screen
                return true;
            }
            
            return false;
        });
    }
}
