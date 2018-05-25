package org.usfirst.frc.team4999.gui;

import java.awt.BorderLayout;
import java.awt.Container;

import javax.swing.JFrame;

public class Main {

	public static void main(String[] args) {
		JFrame window = new JFrame();
		window.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		window.setTitle("Slack-Debloater");
		Container windowContainer = window.getContentPane();
		windowContainer.setLayout(new BorderLayout());
		
		ModeSwitcher mode = new ModeSwitcher();
		window.add(mode, BorderLayout.WEST);
		window.add(mode.getSelected(), BorderLayout.CENTER);
		
		window.setVisible(true);
		
	}

}
