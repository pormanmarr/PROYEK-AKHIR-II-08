import 'package:flutter_test/flutter_test.dart';
import 'package:tk_mutiara/main.dart';

void main() {
  testWidgets('App loads successfully', (WidgetTester tester) async {
    await tester.pumpWidget(const MutiaraApp()); 

    expect(find.text('TK Swasta Mutiara Balige'), findsWidgets);
  });
}